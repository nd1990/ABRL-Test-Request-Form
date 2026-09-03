<?php

namespace App\Jobs;

use App\Mail\NewQuotationNotificationMail;
use App\Models\Admin;
use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyAdminsNewQuotation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;

    public int $tries = 3;

    public function __construct(public Quotation $quotation) {}

    public function handle(): void
    {
        $admins = Admin::where('is_active', true)->get();

        if ($admins->isEmpty()) {
            return;
        }

        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email, $admin->name)->send(new NewQuotationNotificationMail($this->quotation, $admin));
            } catch (\Throwable $e) {
                Log::error('Admin notification failed for ' . $admin->email . ' (quotation ' . $this->quotation->quotation_number . '): ' . $e->getMessage());
            }
        }
    }
}
