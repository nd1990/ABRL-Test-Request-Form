@extends('admin.layouts.app')
@section('title', 'Manage Users')
@section('content')
<div x-data="permissionsViewer({{ Js::from($modalUsers) }}, {{ Js::from($modules) }})" class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manage Users</h1>
            <p class="text-sm text-gray-500 mt-1">Create and manage admin users for the panel</p>
        </div>
        @if($currentAdmin?->hasPermission('users.create'))
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-lg shadow-brand-500/25 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
            Add User
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-rose-50 border border-rose-200 text-rose-700 rounded-xl px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/80">
                        <th class="text-left font-semibold text-gray-500 px-5 py-3">Name</th>
                        <th class="text-left font-semibold text-gray-500 px-5 py-3">Email</th>
                        <th class="text-left font-semibold text-gray-500 px-5 py-3">Role</th>
                        <th class="text-center font-semibold text-gray-500 px-5 py-3">Status</th>
                        <th class="text-center font-semibold text-gray-500 px-5 py-3">Permissions</th>
                        <th class="text-left font-semibold text-gray-500 px-5 py-3">Created</th>
                        <th class="text-right font-semibold text-gray-500 px-5 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50/50 {{ $user->id == session('admin_id') ? 'bg-brand-50/40' : '' }}">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-600 text-xs font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                    @if($user->id == session('admin_id'))
                                    <span class="text-[11px] text-brand-600 font-medium">(You)</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $user->email }}</td>
                        <td class="px-5 py-3">
                            @if($user->isMaster())
                            <span class="inline-flex items-center text-xs font-semibold bg-violet-50 text-violet-700 px-2.5 py-1 rounded-full">Master Admin</span>
                            @else
                            <span class="inline-flex items-center text-xs font-semibold bg-brand-50 text-brand-700 px-2.5 py-1 rounded-full">User</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($user->is_active)
                            <span class="inline-flex items-center text-xs font-semibold bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-full">Active</span>
                            @else
                            <span class="inline-flex items-center text-xs font-semibold bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full">Inactive</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($user->isMaster())
                            <span class="inline-flex items-center text-xs font-medium text-gray-500">All modules</span>
                            @else
                            <span class="inline-flex items-center text-xs font-medium text-gray-600">{{ $user->moduleCount() }} module{{ $user->moduleCount() !== 1 ? 's' : '' }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500">{{ $user->created_at ? $user->created_at->format('d M Y') : '—' }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button type="button" @click="openModal({{ $user->id }})" title="View Permissions"
                                    class="inline-flex items-center justify-center w-8 h-8 text-gray-500 hover:text-brand-600 bg-gray-50 hover:bg-brand-50 rounded-lg transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                                </button>
                                @if($user->isMaster())
                                <span class="text-xs text-gray-400 italic px-2">Protected</span>
                                @else
                                    @if($currentAdmin?->hasPermission('users.edit'))
                                    <a href="{{ route('admin.users.edit', $user->id) }}" title="Edit"
                                        class="inline-flex items-center justify-center w-8 h-8 text-brand-600 hover:text-brand-800 bg-brand-50 hover:bg-brand-100 rounded-lg transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </a>
                                    @endif
                                    @if($currentAdmin?->hasPermission('users.delete'))
                                    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Delete"
                                            class="inline-flex items-center justify-center w-8 h-8 text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 rounded-lg transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                                        </button>
                                    </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    <!-- View Permissions Modal -->
    <div x-show="modalOpen" x-cloak x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="closeModal()"></div>
        <div x-show="modalOpen" x-cloak x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto" style="display:none;">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-2xl flex items-center justify-between z-10">
                <div>
                    <h3 class="text-lg font-bold text-gray-900" x-text="current?.name || 'User'"></h3>
                    <p class="text-sm text-gray-500" x-text="current?.email || ''"></p>
                </div>
                <button @click="closeModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="p-6">
                <template x-if="current && current.is_master">
                    <div class="bg-violet-50 border border-violet-200 rounded-xl p-4">
                        <p class="text-sm font-semibold text-violet-800">Master Admin</p>
                        <p class="text-xs text-violet-600 mt-0.5">This account automatically has full access to every module. Permissions do not apply.</p>
                    </div>
                </template>
                <template x-if="current && !current.is_master && current.permissions.length === 0">
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                        <p class="text-sm font-semibold text-amber-800">No permissions granted</p>
                        <p class="text-xs text-amber-600 mt-0.5">This user can log in but cannot access any module until permissions are granted.</p>
                    </div>
                </template>
                <div class="space-y-3" x-show="current && !current.is_master && current.permissions.length > 0">
                    <template x-for="mod in modules" :key="mod.key">
                        <div x-show="modGrants(mod).length > 0" class="border border-gray-200 rounded-xl overflow-hidden">
                            <div class="px-4 py-2.5 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                                <p class="text-sm font-semibold text-gray-800" x-text="mod.label"></p>
                                <span x-text="modGrants(mod).length" class="text-[11px] font-semibold text-brand-700 bg-brand-50 px-2 py-0.5 rounded-full"></span>
                            </div>
                            <div class="px-4 py-3 flex flex-wrap gap-1.5">
                                <template x-for="grant in modGrants(mod)" :key="grant.key">
                                    <span class="inline-flex items-center gap-1 text-[11px] font-medium text-gray-700 bg-gray-100 rounded-full px-2.5 py-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                        <span x-text="grant.label"></span>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('permissionsViewer', (users, modules) => ({
        users,
        modules: Object.entries(modules).map(([key, mod]) => ({ key, label: mod.label, perms: Object.entries(mod.permissions).map(([pkey, plabel]) => ({ key: pkey, label: plabel })) })),
        modalOpen: false,
        current: null,

        openModal(id) {
            const user = this.users.find(u => String(u.id) === String(id));
            if (!user) return;
            this.current = user;
            this.modalOpen = true;
        },
        closeModal() { this.modalOpen = false; },

        modGrants(mod) {
            if (!this.current || this.current.is_master) return [];
            return mod.perms.filter(p => this.current.permissions.includes(p.key));
        }
    }));
});
</script>
@endsection