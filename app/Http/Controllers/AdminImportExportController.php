<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminImportExportController extends Controller
{
    public function __construct(protected AuditLogService $auditLog) {}

    public function quotationsExport(Request $request)
    {
        $admin = app('admin');

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

        $rows = $query->get()->map(function ($q) {
            return [
                'Quotation #' => $q->quotation_number,
                'Date' => $q->quotation_date?->format('Y-m-d'),
                'Client' => $q->client_name,
                'Company' => $q->company_name,
                'Email' => $q->email,
                'Phone' => $q->phone,
                'Subtotal' => $q->subtotal,
                'Discount' => $q->discount,
                'Tax' => $q->tax,
                'Grand Total' => $q->grand_total,
                'Status' => $q->status,
                'Email Status' => $q->email_status,
            ];
        })->toArray();

        $this->auditLog->log($admin, 'quotations.exported', 'Quotation', null, null, ['count' => count($rows)], $request);

        $format = $request->input('format', 'xlsx') === 'csv' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX;

        return Excel::download(new \App\Exports\QuotationsExport($rows), 'quotations-' . now()->format('Y-m-d') . ($format === \Maatwebsite\Excel\Excel::CSV ? '.csv' : '.xlsx'), $format);
    }
}
