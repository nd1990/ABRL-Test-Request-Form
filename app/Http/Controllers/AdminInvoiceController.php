<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Quotation;
use App\Services\AuditLogService;
use App\Services\InvoiceService;
use App\Services\PdfService;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminInvoiceController extends Controller
{
    public function __construct(
        protected InvoiceService $invoiceService,
        protected PdfService $pdfService,
        protected AuditLogService $auditLog,
        protected SettingsService $settings
    ) {}

    protected function buildIndexQuery(Request $request)
    {
        $query = Invoice::with('quotation')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->input('date_to'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return $query;
    }

    public function index(Request $request)
    {
        $invoices = $this->buildIndexQuery($request)->paginate(15)->withQueryString();

        return view('admin.invoices.index', [
            'invoices' => $invoices,
            'filters' => $request->only(['search', 'date_from', 'date_to', 'status']),
        ]);
    }

    public function data(Request $request)
    {
        $invoices = $this->buildIndexQuery($request)
            ->paginate(15, ['*'], 'page', max(1, (int) $request->input('page', 1)));

        $data = [
            'rows' => view('admin.invoices._rows', ['invoices' => $invoices])->render(),
            'pagination' => view('admin.invoices._pagination', ['invoices' => $invoices])->render(),
            'total' => $invoices->total(),
            'from' => $invoices->firstItem() ?: 0,
            'to' => $invoices->lastItem() ?: 0,
            'current_page' => $invoices->currentPage(),
            'last_page' => $invoices->lastPage(),
            'has_pages' => $invoices->hasPages(),
            'count' => $invoices->count(),
            'ids' => $invoices->map(fn($i) => $i->id)->values()->all(),
        ];

        return response()->json($data);
    }

    public function create(Quotation $quotation)
    {
        if ($quotation->status !== 'accepted') {
            return redirect()->route('admin.quotations.show', $quotation)
                ->with('error', 'Final invoices can only be generated from accepted quotations.');
        }

        $eligibleItems = $this->invoiceService->eligibleItems($quotation);

        if ($eligibleItems->isEmpty()) {
            return redirect()->route('admin.quotations.show', $quotation)
                ->with('error', 'All parameters on this quotation have already been invoiced.');
        }

        $currency = $this->settings->currencySymbol();

        return view('admin.invoices.create', compact('quotation', 'eligibleItems', 'currency'));
    }

    public function store(Request $request, Quotation $quotation)
    {
        $admin = app('admin');

        try {
            $invoice = $this->invoiceService->create($quotation, $request->input('items', []), $admin);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        $this->pdfService->regenerateInvoice($invoice);
        $this->auditLog->invoiceGenerated($admin, $invoice, $request);

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', 'Invoice ' . $invoice->invoice_number . ' generated successfully. Only the selected parameters were billed.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('items', 'quotation', 'creator');
        $company = $this->settings->company();
        $payment = $this->settings->payment();
        $currency = $this->settings->currencySymbol();

        return view('admin.invoices.show', compact('invoice', 'company', 'payment', 'currency'));
    }

    public function pdf(Invoice $invoice)
    {
        $admin = app('admin');

        if (!$invoice->pdf_path || !\Illuminate\Support\Facades\Storage::disk('local')->exists($invoice->pdf_path)) {
            $this->pdfService->regenerateInvoice($invoice);
        }

        $this->auditLog->invoicePdfGenerated($admin, $invoice, request());

        return $this->pdfService->downloadInvoice($invoice);
    }

    public function destroy(Invoice $invoice)
    {
        $admin = app('admin');

        $this->invoiceService->delete($invoice);
        $this->auditLog->invoiceDeleted($admin, $invoice, request());

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice deleted. Its parameters are available for re-invoicing.');
    }
}