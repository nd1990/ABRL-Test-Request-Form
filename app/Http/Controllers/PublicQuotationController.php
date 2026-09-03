<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuotationRequest;
use App\Jobs\NotifyAdminsNewQuotation;
use App\Models\Quotation;
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

            $pdfPath = $this->pdfService->regenerate($quotation);

            $emailSent = $this->emailService->sendQuotation($quotation);

            if ((bool) ($this->settings->email()['notify_new_quotation'] ?? true)) {
                NotifyAdminsNewQuotation::dispatch($quotation);
            }

            $downloadToken = csrf_token() . '|' . $quotation->id;
            Session::put('quotation_download_' . $quotation->id, true);

            RateLimiter::hit($key, 3600);

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