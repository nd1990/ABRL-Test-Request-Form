<?php

namespace App\Jobs;

use App\Mail\QuotationMail;
use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendQuotationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 60;

    public int $tries = 3;

    public function __construct(public Quotation $quotation) {}

    public function handle(): void
    {
        try {
            if (!filter_var($this->quotation->email, FILTER_VALIDATE_EMAIL)) {
                $this->quotation->update(['email_status' => 'failed']);

                return;
            }

            Mail::to($this->quotation->email, $this->quotation->client_name)->send(new QuotationMail($this->quotation));

            $this->quotation->update([
                'email_status' => 'sent',
                'email_sent_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Quotation email failed for ' . $this->quotation->quotation_number . ': ' . $e->getMessage());
            $this->quotation->update(['email_status' => 'failed']);

            $this->fail($e);
        }
    }
}
