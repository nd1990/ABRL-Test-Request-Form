@extends('admin.layouts.app')
@section('title', 'Settings')
@section('content')
<div class="max-w-2xl">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-11 h-11 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 110 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notification Settings</h1>
            <p class="text-sm text-gray-500 mt-0.5">Configure email alerts sent to admins when a new quotation is submitted</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
        {{ session('success') }}
    </div>
    @endif

    @if(!$currentAdmin?->hasPermission('settings.edit'))
    <div class="mb-4 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-sm text-amber-700 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        You do not have permission to edit these settings. Showing current values in read-only mode.
    </div>
    @endif
    <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-6" @if(!$currentAdmin?->hasPermission('settings.edit')) onsubmit="return false" @endif>
        @csrf
        @method('PUT')

        @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 rounded-xl p-4">
            <p class="text-sm font-semibold text-rose-700 mb-1">Please fix the following errors:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                <li class="text-xs text-rose-600">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-800">Notify admins on new quotation</label>
                    <p class="text-xs text-gray-500 mt-1">When enabled, an email alert is sent to every active admin (including the Master Admin) each time a user submits a quotation request on the public form.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox" name="notify_new_quotation" value="1"
                        {{ $enabled ? 'checked' : '' }}
                        @if(!$currentAdmin?->hasPermission('settings.edit')) disabled @endif
                        class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-checked:bg-brand-600 rounded-full peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border after:border-gray-300 after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                </label>
            </div>
        </div>

        <div>
            <label for="notify_new_quotation_subject" class="block text-sm font-semibold text-gray-800 mb-1.5">Email Subject</label>
            <input type="text" id="notify_new_quotation_subject" name="notify_new_quotation_subject" required maxlength="191"
                value="{{ old('notify_new_quotation_subject', $subject) }}"
                @if(!$currentAdmin?->hasPermission('settings.edit')) readonly disabled @endif
                class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition disabled:bg-gray-50 disabled:text-gray-500">
            <p class="text-xs text-gray-500 mt-1.5">Available placeholders: <code class="text-brand-600 bg-brand-50 px-1 rounded">@{{quotation_number}}</code> &nbsp;<code class="text-brand-600 bg-brand-50 px-1 rounded">@{{company_name}}</code></p>
        </div>

        @if($currentAdmin?->isMaster())
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
            <p class="text-sm font-semibold text-gray-800 mb-2">Notification recipients</p>
            @if($admins->count())
                <ul class="space-y-1.5">
                    @foreach($admins as $admin)
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <span class="w-6 h-6 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center text-[10px] font-bold shrink-0">{{ strtoupper(substr($admin->name, 0, 1)) }}</span>
                            <span class="font-medium text-gray-800">{{ $admin->name }}</span>
                            <span class="text-gray-400">&lt;{{ $admin->email }}&gt;</span>
                            <span class="ml-auto text-[11px] uppercase font-semibold text-gray-400">{{ $admin->roleLabel() }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-500">No active admins found.</p>
            @endif
        </div>
        @else
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
            <p class="text-sm font-semibold text-gray-800 mb-2">Notification recipients</p>
            <p class="text-xs text-gray-500">New quotations are notified to every active admin. The recipient list is visible to the Master Admin only.</p>
        </div>
        @endif

        @if($currentAdmin?->hasPermission('settings.edit'))
        <div class="flex items-center justify-end pt-2">
            <button type="submit"
                class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-lg shadow-brand-500/25 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Save Settings
            </button>
        </div>
        @endif
    </form>

    <hr class="my-8 border-gray-200">

    <div class="flex items-center gap-3 mb-6">
        <div class="w-11 h-11 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Change Password</h2>
            <p class="text-sm text-gray-500 mt-0.5">Update the password for your account</p>
        </div>
    </div>

    @if(session('pw_success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm">{{ session('pw_success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.password.change.submit') }}" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-5 max-w-lg">
        @csrf

        @if($errors->password->any())
        <div class="bg-rose-50 border border-rose-200 rounded-xl p-4">
            <p class="text-sm font-semibold text-rose-700 mb-1">Please fix the following errors:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->password->all() as $error)
                <li class="text-xs text-rose-600">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1.5">Current Password <span class="text-rose-500">*</span></label>
            <input type="password" id="current_password" name="current_password" required
                class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">New Password <span class="text-rose-500">*</span></label>
            <input type="password" id="password" name="password" required minlength="8" placeholder="Minimum 8 characters"
                class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirm New Password <span class="text-rose-500">*</span></label>
            <input type="password" id="password_confirmation" name="password_confirmation" required
                class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
        </div>

        <div class="flex items-center justify-end pt-2">
            <button type="submit"
                class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-lg shadow-brand-500/25 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Update Password
            </button>
        </div>
    </form>
</div>
@endsection
