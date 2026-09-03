<?php

namespace App\Services;

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

    protected function numberToWords(float $number): string
    {
        $f = new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT);

        return ucfirst($f->format($number));
    }
}
