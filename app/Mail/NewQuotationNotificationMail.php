<?php

namespace App\Mail;

use App\Models\Admin;
use App\Models\Quotation;
use App\Services\SettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class NewQuotationNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Quotation $quotation, public Admin $recipientAdmin) {}

    public function envelope(): Envelope
    {
        $settings = app(SettingsService::class);
        $company = $settings->company();
        $emailSettings = $settings->email();

        $subject = str_replace(
            ['{{quotation_number}}', '{{company_name}}'],
            [$this->quotation->quotation_number, $company['name'] ?? ''],
            $emailSettings['notify_new_quotation_subject'] ?? 'New Quotation {{quotation_number}} received'
        );

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $settings = app(SettingsService::class);
        $company = $settings->company();
        $currency = $settings->currencySymbol();

        return new Content(view: 'emails.admin-new-quotation', with: [
            'quotation' => $this->quotation,
            'admin' => $this->recipientAdmin,
            'company' => $company,
            'currency' => $currency,
            'adminUrl' => route('admin.quotations.show', $this->quotation),
        ]);
    }

    public function attachments(): array
    {
        if (
            $this->quotation->pdf_path &&
            Storage::disk('local')->exists($this->quotation->pdf_path)
        ) {
            return [
                Attachment::fromPath(Storage::disk('local')->path($this->quotation->pdf_path))
                    ->as('Quotation_' . $this->quotation->quotation_number . '.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
