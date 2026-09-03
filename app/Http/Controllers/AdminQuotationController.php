<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateQuotationRequest;
use App\Models\Quotation;
use App\Models\Service;
use App\Services\AuditLogService;
use App\Services\EmailService;
use App\Services\PdfService;
use App\Services\QuotationService;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminQuotationController extends Controller
{
    public function __construct(
        protected QuotationService $quotationService,
        protected PdfService $pdfService,
        protected EmailService $emailService,
        protected AuditLogService $auditLog,
        protected SettingsService $settings
    ) {}

    protected function buildIndexQuery(Request $request)
    {
        $query = Quotation::with('items')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('quotation_number', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('quotation_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('quotation_date', '<=', $request->input('date_to'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('email_status')) {
            $query->where('email_status', $request->input('email_status'));
        }

        return $query;
    }

    public function index(Request $request)
    {
        $quotations = $this->buildIndexQuery($request)->paginate(15)->withQueryString();

        return view('admin.quotations.index', [
            'quotations' => $quotations,
            'filters' => $request->only(['search', 'date_from', 'date_to', 'status', 'email_status']),
        ]);
    }

    public function data(Request $request)
    {
        $quotations = $this->buildIndexQuery($request)
            ->paginate(15, ['*'], 'page', max(1, (int) $request->input('page', 1)));

        $data = [
            'rows' => view('admin.quotations._rows', ['quotations' => $quotations])->render(),
            'pagination' => view('admin.quotations._pagination', ['quotations' => $quotations])->render(),
            'total' => $quotations->total(),
            'from' => $quotations->firstItem() ?: 0,
            'to' => $quotations->lastItem() ?: 0,
            'current_page' => $quotations->currentPage(),
            'last_page' => $quotations->lastPage(),
            'has_pages' => $quotations->hasPages(),
            'count' => $quotations->count(),
        ];

        return response()->json($data);
    }

    public function show(Quotation $quotation)
    {
        $quotation->load('items', 'creator');
        $company = $this->settings->company();
        $payment = $this->settings->payment();
        $currency = $this->settings->currencySymbol();

        return view('admin.quotations.show', compact('quotation', 'company', 'payment', 'currency'));
    }

    public function edit(Quotation $quotation)
    {
        $quotation->load('items');
        $services = Service::active()->orderBy('sort_order')->orderBy('name')->get();
        $currency = $this->settings->currencySymbol();
        $quotationSettings = $this->settings->quotation();

        return view('admin.quotations.edit', compact('quotation', 'services', 'currency', 'quotationSettings'));
    }

    public function update(UpdateQuotationRequest $request, Quotation $quotation)
    {
        $data = $request->validated();

        $old = $quotation->only(['client_name', 'company_name', 'email', 'phone', 'gst_number', 'status', 'subtotal', 'grand_total']);
        $oldValue = $quotation->grand_total;

        $quotation = $this->quotationService->update($quotation, $data);

        $quotation->update(['status' => $data['status'] ?? 'sent']);

        $oldStatus = $old['status'] ?? 'sent';

        $admin = app('admin');
        $this->auditLog->quotationUpdated($admin, $quotation, $old, $request);

        if (($oldStatus ?? 'sent') !== ($data['status'] ?? 'sent')) {
            $this->auditLog->statusChanged($admin, $quotation, $oldStatus, $data['status'], $request);
        }

        if ($request->input('regenerate_pdf')) {
            $this->pdfService->regenerate($quotation);
            $this->auditLog->pdfGenerated($admin, $quotation, $request);
        }

        if ($request->input('resend_email')) {
            $ok = $this->emailService->sendQuotation($quotation);
            $this->auditLog->emailResent($admin, $quotation, $request);
        }

        return redirect()->route('admin.quotations.index')->with('success', 'Quotation updated successfully.');
    }

    public function pdf(Quotation $quotation)
    {
        $admin = app('admin');

        if (!$quotation->pdf_path || !\Illuminate\Support\Facades\Storage::disk('local')->exists($quotation->pdf_path)) {
            $this->pdfService->regenerate($quotation);
        }

        $this->auditLog->pdfGenerated($admin, $quotation, request());

        return $this->pdfService->download($quotation);
    }

    public function resend(Quotation $quotation)
    {
        $admin = app('admin');

        if (!$quotation->pdf_path || !\Illuminate\Support\Facades\Storage::disk('local')->exists($quotation->pdf_path)) {
            $this->pdfService->regenerate($quotation);
        }

        $ok = $this->emailService->sendQuotation($quotation);
        $this->auditLog->emailResent($admin, $quotation, request());

        return back()->with($ok ? 'success' : 'error', $ok
            ? 'Quotation email resent successfully.'
            : 'Email could not be sent. Check SMTP settings and try again.');
    }

    public function duplicate(Quotation $quotation)
    {
        $admin = app('admin');

        $copy = $this->quotationService->duplicate($quotation);
        $this->pdfService->regenerate($copy);
        $this->auditLog->quotationCreated($admin, $copy, request());

        return redirect()->route('admin.quotations.show', $copy)->with('success', 'Quotation duplicated. New number: ' . $copy->quotation_number);
    }

    public function destroy(Quotation $quotation)
    {
        $admin = app('admin');

        $quotation->items()->delete();
        $quotation->delete();

        $this->auditLog->quotationDeleted($admin, $quotation, request());

        return redirect()->route('admin.quotations.index')->with('success', 'Quotation deleted permanently.');
    }
}