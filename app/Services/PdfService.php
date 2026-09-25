<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    public function __construct(protected SettingsService $settings) {}

    protected function makePdf(array $data)
    {
        return Pdf::loadView('pdf.quotation', $data)
            ->setPaper('a4')
            ->setOption('isPhpEnabled', true);
    }

    protected function makeInvoicePdf(array $data)
    {
        return Pdf::loadView('pdf.invoice', $data)
            ->setPaper('a4')
            ->setOption('isPhpEnabled', true);
    }

    public function generate(Quotation $quotation): string
    {
        $data = $this->pdfData($quotation);
        $pdf = $this->makePdf($data);
        $fileName = 'quotations/' . $quotation->quotation_number . '.pdf';

        Storage::disk('local')->put($fileName, $pdf->output());

        return $fileName;
    }

    public function regenerate(Quotation $quotation): string
    {
        $old = $quotation->pdf_path;

        if ($old) {
            Storage::disk('local')->delete($old);
        }

        $path = $this->generate($quotation);
        $quotation->update(['pdf_path' => $path]);

        return $path;
    }

    public function stream(Quotation $quotation)
    {
        $data = $this->pdfData($quotation);

        return $this->makePdf($data)->stream();
    }

    public function download(Quotation $quotation)
    {
        if ($quotation->pdf_path && Storage::disk('local')->exists($quotation->pdf_path)) {
            return Storage::disk('local')->download($quotation->pdf_path);
        }

        $data = $this->pdfData($quotation);

        return $this->makePdf($data)->download($quotation->quotation_number . '.pdf');
    }

    protected function pdfData(Quotation $quotation): array
    {
        return [
            'quotation' => $quotation,
            'company' => $this->settings->company(),
            'payment' => $this->settings->payment(),
            'pdfSettings' => $this->settings->pdf(),
            'currency' => $this->settings->currencySymbol(),
            'amountInWords' => $this->numberToWords((float) $quotation->grand_total),
        ];
    }

    public function generateInvoice(Invoice $invoice): string
    {
        $data = $this->invoicePdfData($invoice);
        $pdf = $this->makeInvoicePdf($data);
        $fileName = 'invoices/' . $invoice->invoice_number . '.pdf';

        Storage::disk('local')->put($fileName, $pdf->output());

        return $fileName;
    }

    public function regenerateInvoice(Invoice $invoice): string
    {
        $old = $invoice->pdf_path;

        if ($old) {
            Storage::disk('local')->delete($old);
        }

        $path = $this->generateInvoice($invoice);
        $invoice->update(['pdf_path' => $path]);

        return $path;
    }

    public function downloadInvoice(Invoice $invoice)
    {
        if ($invoice->pdf_path && Storage::disk('local')->exists($invoice->pdf_path)) {
            return Storage::disk('local')->download($invoice->pdf_path);
        }

        $data = $this->invoicePdfData($invoice);

        return $this->makeInvoicePdf($data)->download($invoice->invoice_number . '.pdf');
    }

    protected function invoicePdfData(Invoice $invoice): array
    {
        $invoice->loadMissing('items.labTest', 'quotation');

        return [
            'invoice' => $invoice,
            'company' => $this->settings->company(),
            'payment' => $this->settings->payment(),
            'pdfSettings' => $this->settings->pdf(),
            'currency' => $this->settings->currencySymbol(),
            'amountInWords' => $this->numberToWords((float) $invoice->grand_total),
        ];
    }

    protected function numberToWords(float $number): string
    {
        $f = new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT);

        return ucfirst($f->format($number));
    }
}
