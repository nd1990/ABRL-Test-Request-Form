@extends('admin.layouts.app')
@section('title', 'Manage Users')
@section('content')
<div x-data="usersList()">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manage Users</h1>
            <p class="text-sm text-gray-500 mt-1">Create and manage admin users for the panel</p>
        </div>
        <button @click="openAdd()" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-lg shadow-brand-500/25 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
            Add User
        </button>
    </div>

    @if(session('success'))
    <div class="mt-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="mt-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <div class="mt-6 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/80">
                        <th class="text-left font-semibold text-gray-500 px-5 py-3">Name</th>
                        <th class="text-left font-semibold text-gray-500 px-5 py-3">Email</th>
                        <th class="text-left font-semibold text-gray-500 px-5 py-3">Role</th>
                        <th class="text-center font-semibold text-gray-500 px-5 py-3">Status</th>
                        <th class="text-center font-semibold text-gray-500 px-5 py-3">Last Login</th>
                        <th class="text-right font-semibold text-gray-500 px-5 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50/50" :class="{'bg-gray-50': '{{ $user->id }}' == '{{ session('admin_id') }}'}">
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
                            @if($user->role === 'master')
                            <span class="inline-flex items-center text-xs font-semibold bg-violet-50 text-violet-700 px-2.5 py-1 rounded-full">Master Admin</span>
                            @else
                            <span class="inline-flex items-center text-xs font-semibold bg-brand-50 text-brand-700 px-2.5 py-1 rounded-full">Admin</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($user->is_active)
                            <span class="inline-flex items-center text-xs font-semibold bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-full">Active</span>
                            @else
                            <span class="inline-flex items-center text-xs font-semibold bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full">Inactive</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center text-xs text-gray-500">{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : 'Never' }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($user->role === 'master')
                                <span class="text-xs text-gray-400 italic">Protected</span>
                                @else
                                <button type="button" @click="openEdit({{ Js::from($user->toArray()) }})" title="Edit"
                                    class="inline-flex items-center justify-center w-8 h-8 text-brand-600 hover:text-brand-800 bg-brand-50 hover:bg-brand-100 rounded-lg transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </button>
                                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete"
                                        class="inline-flex items-center justify-center w-8 h-8 text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 rounded-lg transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                                    </button>
                                </form>
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

    <!-- Add/Edit Modal -->
    <div x-show="modalOpen" x-cloak x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="closeModal()"></div>
        <div x-show="modalOpen" x-cloak x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto" style="display:none;">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-2xl flex items-center justify-between z-10">
                <h3 x-text="modalTitle" class="text-lg font-bold text-gray-900"></h3>
                <button @click="closeModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <form :action="modalAction" method="POST" class="p-6 space-y-5">
                @csrf
                <input type="hidden" name="_method" :value="modalMethod">

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
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Full Name <span class="text-rose-500">*</span></label>
                    <input type="text" id="name" name="name" x-model="current.name" required placeholder="Enter full name"
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email Address <span class="text-rose-500">*</span></label>
                    <input type="email" id="email" name="email" x-model="current.email" required placeholder="Enter email"
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1.5">Role <span class="text-rose-500">*</span></label>
                    <select id="role" name="role" x-model="current.role" required
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition bg-white">
                        <option value="admin">Admin</option>
                        <option value="master">Master Admin</option>
                    </select>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5" x-text="current.id ? 'Password (leave blank to keep current)' : 'Password'"></label>
                    <input type="password" id="password" name="password" x-model="current.password" :required="!current.id" minlength="8" placeholder="Minimum 8 characters"
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" x-model="current.password_confirmation" :required="!current.id"
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                </div>

                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" :checked="current.is_active"
                            class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <button type="button" @click="closeModal()"
                        class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold px-5 py-2.5 rounded-xl transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-lg shadow-brand-500/25 transition">
                        <span x-text="current.id ? 'Save Changes' : 'Create User'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('usersList', () => ({
        modalOpen: false,
        modalAction: '{{ route('admin.users.store') }}',
        modalMethod: 'POST',
        modalTitle: 'Add User',
        current: { id:'', name:'', email:'', password:'', password_confirmation:'', role:'admin', is_active:true },

        openAdd() {
            this.modalAction = '{{ route('admin.users.store') }}';
            this.modalMethod = 'POST';
            this.modalTitle = 'Add User';
            this.current = { id:'', name:'', email:'', password:'', password_confirmation:'', role:'admin', is_active:true };
            this.modalOpen = true;
        },
        openEdit(u) {
            this.modalAction = '{{ route('admin.users.update', '__ID__') }}'.replace('__ID__', u.id);
            this.modalMethod = 'PUT';
            this.modalTitle = 'Edit User';
            this.current = { id:u.id, name:u.name, email:u.email, password:'', password_confirmation:'', role:u.role, is_active:!!u.is_active };
            this.modalOpen = true;
        },
        closeModal() { this.modalOpen = false; }
    }));
});
</script>
@endsection
