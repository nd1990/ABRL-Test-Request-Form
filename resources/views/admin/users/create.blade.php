@extends('admin.layouts.app')
@section('title', 'Add User')
@section('content')
@php
    $moduleSpec = collect(config('permissions.modules'))->map(function ($m, $k) {
        return [
            'key' => $k,
            'label' => $m['label'],
            'perms' => collect($m['permissions'])->map(fn ($label, $key) => ['key' => $key, 'label' => $label])->values(),
        ];
    })->values()->all();
@endphp

<div class="max-w-4xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.users.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Add User</h1>
            <p class="text-sm text-gray-500 mt-0.5">Create a new admin panel account</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6"
        x-data="permissionPicker({{ Js::from([]) }}, {{ Js::from($moduleSpec) }}, 'user')">
        @csrf

        @if($errors->any())
        <div class="bg-white rounded-2xl border border-rose-200 shadow-sm p-5">
            <p class="text-sm font-semibold text-rose-700 mb-2">Please fix the following errors:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                @if($error === $errors->first('permissions')) @continue @endif
                <li class="text-sm text-rose-600">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Account Details</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Full Name <span class="text-rose-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Enter full name"
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email Address <span class="text-rose-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="Enter email"
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1.5">Role <span class="text-rose-500">*</span></label>
                    <select id="role" name="role" x-model="role" required
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition bg-white">
                        <option value="user">User</option>
                        <option value="master_admin">Master Admin</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1.5">Master Admin accounts bypass all permission checks.</p>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password <span class="text-rose-500">*</span></label>
                    <input type="password" id="password" name="password" required minlength="8" placeholder="Minimum 8 characters"
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password <span class="text-rose-500">*</span></label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                </div>
                <div>
                    <label class="flex items-center gap-3 cursor-pointer mt-7">
                        <input type="checkbox" name="is_active" value="1" checked
                            class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6" x-show="role === 'master_admin'" x-cloak>
            <div class="flex items-start gap-3 bg-violet-50 border border-violet-200 rounded-xl p-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0 mt-0.5 text-violet-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <div>
                    <p class="text-sm font-semibold text-violet-800">Master Admin Account</p>
                    <p class="text-xs text-violet-600 mt-0.5">This account will automatically have full access to every module. No permissions need to be selected.</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            @include('admin.users._permissions', ['selected' => []])
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.users.index') }}"
                class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold px-5 py-2.5 rounded-xl transition">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-lg shadow-brand-500/25 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
                Create User
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('permissionPicker', (initialSelected, moduleList, initialRole) => ({
        role: initialRole,
        modules: moduleList,
        selected: Array.isArray(initialSelected) ? [...initialSelected] : [],
        enabled: {},

        init() {
            this.modules.forEach(mod => {
                this.enabled[mod.key] = mod.perms.some(p => this.selected.includes(p.key));
            });
        },

        isEnabled(mod) { return !!this.enabled[mod.key]; },
        hasAnySelected(mod) { return mod.perms.some(p => this.selected.includes(p.key)); },
        moduleSelectedCount(mod) { return mod.perms.filter(p => this.selected.includes(p.key)).length; },

        toggleModule(mod, checked) {
            this.enabled[mod.key] = checked;
            if (!checked) {
                const keys = mod.perms.map(p => p.key);
                this.selected = this.selected.filter(k => !keys.includes(k));
            }
        },

        selectAll() {
            this.modules.forEach(mod => { this.enabled[mod.key] = true; });
            this.selected = this.modules.flatMap(mod => mod.perms.map(p => p.key));
        },

        clearAll() {
            this.modules.forEach(mod => { this.enabled[mod.key] = false; });
            this.selected = [];
        }
    }));
});
</script>
@endsection