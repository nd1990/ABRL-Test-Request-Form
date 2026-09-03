<?php

namespace App\Services;

use App\Jobs\SendQuotationEmail;
use App\Models\Quotation;

class EmailService
{
    public function sendQuotation(Quotation $quotation): bool
    {
        if (!filter_var($quotation->email, FILTER_VALIDATE_EMAIL)) {
            $quotation->update(['email_status' => 'failed']);

            return false;
        }

        $quotation->update(['email_status' => 'pending']);

        SendQuotationEmail::dispatch($quotation);

        return true;
    }
}
