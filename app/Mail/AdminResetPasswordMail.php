<?php

namespace App\Mail;

use App\Models\Admin;
use App\Services\SettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Admin $admin, public string $token) {}

    public function envelope(): Envelope
    {
        $settings = app(SettingsService::class);
        $company = $settings->company();

        return new Envelope(
            subject: 'Reset your ' . ($company['name'] ?? 'Admin') . ' password'
        );
    }

    public function content(): Content
    {
        $settings = app(SettingsService::class);
        $company = $settings->company();

        $resetUrl = route('admin.password.reset', [
            'email' => $this->admin->email,
            'token' => $this->token,
        ]);

        return new Content(
            view: 'emails.admin-reset-password',
            with: [
                'admin' => $this->admin,
                'company' => $company,
                'resetUrl' => $resetUrl,
                'expiryMinutes' => 60,
            ]
        );
    }
}
