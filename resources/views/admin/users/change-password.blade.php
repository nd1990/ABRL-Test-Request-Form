@extends('admin.layouts.app')
@section('title', 'Change Password')
@section('content')
<div class="max-w-lg">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-11 h-11 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                @if($currentAdmin?->role === 'master')
                    Master Admin Change Password
                @else
                    Admin Change Password
                @endif
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">Update the password for your account</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.password.change.submit') }}" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-5">
        @csrf

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
