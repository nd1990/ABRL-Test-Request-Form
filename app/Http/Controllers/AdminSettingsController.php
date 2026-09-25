<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Services\AuditLogService;
use App\Services\SettingsService;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function __construct(
        protected SettingsService $settings,
        protected AuditLogService $auditLog
    ) {}

    public function index()
    {
        $emailSettings = $this->settings->email();
        $admins = Admin::where('is_active', true)->where('is_hidden', false)->orderBy('role')->orderBy('name')->get();

        $subject = $emailSettings['notify_new_quotation_subject'] ?? 'New Quotation Request {{quotation_number}}';
        $enabled = ($emailSettings['notify_new_quotation'] ?? '1') === '1';

        return view('admin.settings.index', compact('emailSettings', 'admins', 'subject', 'enabled'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'notify_new_quotation' => ['sometimes', 'boolean'],
            'notify_new_quotation_subject' => ['required', 'string', 'max:191'],
        ]);

        $old = $this->settings->email();

        $email = [
            ...$old,
            'notify_new_quotation' => ($data['notify_new_quotation'] ?? false) ? '1' : '0',
            'notify_new_quotation_subject' => $data['notify_new_quotation_subject'],
        ];

        $this->settings->saveGroup('email', $email);

        $this->auditLog->settingsChanged(app('admin'), 'email', $old, $email, $request);

        return redirect()->route('admin.settings.index')->with('success', 'Notification settings updated successfully.');
    }
}
