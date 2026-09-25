<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuotationRequest;
use App\Jobs\NotifyAdminsNewQuotation;
use App\Models\LabTest;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Service;
use App\Services\EmailService;
use App\Services\PdfService;
use App\Services\QuotationService;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;

class PublicQuotationController extends Controller
{
    public function __construct(
        protected QuotationService $quotationService,
        protected PdfService $pdfService,
        protected EmailService $emailService,
        protected SettingsService $settings
    ) {}

    public function index()
    {
        $services = Service::active()->orderBy('sort_order')->orderBy('name')->get();
        $company = $this->settings->company();
        $currency = $this->settings->currencySymbol();
        $quotationSettings = $this->settings->quotation();

        return view('public.quotation', compact('services', 'company', 'currency', 'quotationSettings'));
    }

    public function services()
    {
        $services = Service::active()->orderBy('sort_order')->orderBy('name')->get();
        $currency = $this->settings->currencySymbol();

        return response()->json([
            'services' => $services,
            'currency' => $currency,
        ]);
    }

    public function store(StoreQuotationRequest $request)
    {
        $data = $request->validated();

        $key = 'quotation:' . ($request->ip() . ':' . ($data['email'] ?? ''));
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json([
                'message' => 'Too many quotation requests. Please try again later.',
            ], 429);
        }

        try {
            $quotation = $this->quotationService->create($data, null);

            $fileUpdates = [];

            if ($request->hasFile('msds_report')) {
                $uploaded = $request->file('msds_report');
                $path = $uploaded->store('quotation-docs/' . $quotation->id, 'public');
                $fileUpdates['msds_report_path'] = $path;
                $fileUpdates['msds_report_name'] = $uploaded->getClientOriginalName();
            }

            if ($request->hasFile('other_documents')) {
                $docs = [];
                foreach ($request->file('other_documents') as $file) {
                    $path = $file->store('quotation-docs/' . $quotation->id, 'public');
                    $docs[] = [
                        'path' => $path,
                        'name' => $file->getClientOriginalName(),
                    ];
                }
                $fileUpdates['other_documents'] = $docs;
            }

            if ($fileUpdates) {
                $quotation->update($fileUpdates);
            }

            $this->pdfService->regenerate($quotation);

            Session::put('quotation_download_' . $quotation->id, true);

            RateLimiter::hit($key, 3600);

            app()->terminating(function () use ($quotation) {
                $this->emailService->sendQuotation($quotation);

                if ((bool) ($this->settings->email()['notify_new_quotation'] ?? true)) {
                    NotifyAdminsNewQuotation::dispatch($quotation);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Quotation generated successfully',
                'quotation_number' => $quotation->quotation_number,
                'email_status' => $quotation->email_status,
                'redirect' => route('quotation.success', $quotation->id),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Quotation creation failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'message' => 'We could not generate your quotation right now. Please try again a little later.',
            ], 500);
        }
    }

    public function preview(Request $request)
    {
        $data = array_merge($request->all(), $request->validate([
            'client_name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'email', 'max:191'],
            'mobile_country' => ['nullable', 'string', 'max:6'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'lab_tests' => ['nullable', 'array'],
            'lab_tests.*.lab_test_id' => ['required_with:lab_tests', 'integer'],
            'lab_tests.*.no_of_samples' => ['required_with:lab_tests', 'integer', 'min:1'],
        ]));

        $data['services'] = [];

        try {
            $calc = $this->quotationService->buildQuotation($data);

            $mobile = trim((string) ($data['mobile'] ?? ''));
            $country = trim((string) ($data['mobile_country'] ?? ''), " \t\n\r\0\x0B+");
            $phone = ($country !== '' && $mobile !== '') ? $country . ' ' . $mobile : ($mobile ?: null);

            $quotation = new Quotation([
                'quotation_number' => $this->quotationService->generateNumber(),
                'client_name' => $data['client_name'],
                'company_name' => $data['company_name'] ?? null,
                'sample_name' => $data['sample_name'] ?? null,
                'sample_batch_no' => $data['sample_batch_no'] ?? null,
                'sample_physical_form' => $data['sample_physical_form'] ?? null,
                'sample_storage_condition' => $data['sample_storage_condition'] ?? null,
                'email' => $data['email'],
                'phone' => $phone,
                'address' => $data['address'] ?? null,
                'address_line2' => $data['address_line2'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'country' => $data['country'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'gst_number' => $data['gst_number'] ?? null,
                'courier_address' => ($data['different_courier_address'] ?? false) ? ($data['courier_address'] ?? null) : null,
                'courier_address_line2' => ($data['different_courier_address'] ?? false) ? ($data['courier_address_line2'] ?? null) : null,
                'courier_city' => ($data['different_courier_address'] ?? false) ? ($data['courier_city'] ?? null) : null,
                'courier_state' => ($data['different_courier_address'] ?? false) ? ($data['courier_state'] ?? null) : null,
                'courier_postal_code' => ($data['different_courier_address'] ?? false) ? ($data['courier_postal_code'] ?? null) : null,
                'courier_country' => ($data['different_courier_address'] ?? false) ? ($data['courier_country'] ?? null) : null,
                'quotation_date' => now()->toDateString(),
                'subtotal' => $calc['subtotal'],
                'discount' => $calc['discount'],
                'tax' => $calc['tax_amount'],
                'grand_total' => $calc['grand_total'],
                'notes' => $data['notes'] ?? null,
            ]);

            $quotation->setRelation('items', collect($calc['items'])->map(function ($item) {
                $quotationItem = new QuotationItem([
                    'service_id' => $item['service_id'],
                    'lab_test_id' => $item['lab_test_id'] ?? null,
                    'service_name_snapshot' => $item['_snapshot_name'],
                    'description_snapshot' => $item['_snapshot_description'],
                    'unit_snapshot' => $item['_snapshot_unit'],
                    'quantity' => $item['quantity'],
                    'unit_price_snapshot' => $item['unit_price'],
                    'discount_snapshot' => $item['discount'],
                    'tax_percentage_snapshot' => $item['tax_percentage'],
                    'tax_amount' => $item['tax_amount'],
                    'total' => $item['total'],
                    'notes' => $item['notes'],
                ]);

                if (!empty($item['lab_test_id'])) {
                    $quotationItem->setRelation('labTest', LabTest::find($item['lab_test_id']));
                }

                return $quotationItem;
            }));

            return $this->pdfService->stream($quotation);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Quotation preview failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'message' => 'We could not prepare the preview right now. Please try again a little later.',
            ], 422);
        }
    }

    public function success(Quotation $quotation)
    {
        if (!Session::has('quotation_download_' . $quotation->id)) {
            abort(404);
        }

        $company = $this->settings->company();
        $currency = $this->settings->currencySymbol();

        return view('public.success', compact('quotation', 'company', 'currency'));
    }

    public function download(Quotation $quotation)
    {
        if (!Session::has('quotation_download_' . $quotation->id)) {
            abort(403);
        }

        return $this->pdfService->download($quotation);
    }
}