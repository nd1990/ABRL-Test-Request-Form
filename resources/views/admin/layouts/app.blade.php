<!DOCTYPE html>
<html lang="en" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50:'#eef2ff', 100:'#e0e7ff', 200:'#c7d2fe', 400:'#4361ee', 500:'#3c50e0', 600:'#3c50e0', 700:'#3056d3', 800:'#264bc8', 900:'#1e40af' },
                        gray: { 50:'#F9FAFB', 100:'#F3F4F6', 200:'#E5E7EB', 300:'#D1D5DB', 400:'#9CA3AF', 500:'#6B7280', 600:'#4B5563', 700:'#374151', 800:'#1F2937', 900:'#111827' }
                    },
                    fontFamily: { sans: ['Inter','ui-sans-serif','system-ui','-apple-system','Segoe UI','Roboto','sans-serif'] }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        [x-cloak]{display:none!important}
        /* TailAdmin-style sidebar menu */
        .menu-item {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 9px 12px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 500;
            line-height: 24px;
            transition: all .2s;
        }
        .menu-item-active { background: #eef2ff; color: #3c50e0; }
        .menu-item-active:hover { background: #e0e7ff; }
        .menu-item-inactive { color: #64748b; }
        .menu-item-inactive:hover { background: #f1f5f9; color: #1e293b; }
        .menu-dropdown-item {
            position: relative;
            display: block;
            padding: 6px 0;
            font-size: 14px;
            font-weight: 500;
            line-height: 20px;
            border-radius: 4px;
            transition: color .2s;
        }
        .menu-dropdown-item-active { color: #3c50e0; }
        .menu-dropdown-item-inactive { color: #64748b; }
        .menu-dropdown-item-inactive:hover { color: #1e293b; }
        /* Custom scrollbar (TailAdmin-style) */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        /* Consistent dropdown arrow for all admin selects */
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 18px;
            padding-right: 2.75rem !important;
        }
        select::-ms-expand { display: none; }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased bg-[#F1F5F9] text-gray-800 min-h-screen">

<div class="flex h-screen overflow-hidden">

    <!-- ===== SIDEBAR ===== -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="sidebar fixed top-0 left-0 z-50 flex h-screen w-[250px] flex-col overflow-y-auto border-r border-gray-200 bg-white px-5 transition-all duration-300 xl:static xl:translate-x-0">

        <!-- SIDEBAR HEADER -->
        <div class="sidebar-header flex items-center gap-2 pt-7 pb-6">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#EEF2FF] text-[#3c50e0]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                </span>
                <span class="text-base font-bold leading-tight tracking-tight text-gray-900">ABRL Test Request Form</span>
            </a>
        </div>

        <!-- SIDEBAR MENU -->
        <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear" style="height: 85%;">
            <nav class="flex flex-col">
                <div>
                    <h3 class="mb-4 text-xs uppercase leading-5 text-gray-400">Menu</h3>
                    <ul class="mb-6 flex flex-col gap-1">
                        <li class="flex">
                            <a href="{{ route('admin.dashboard') }}"
                               class="menu-item {{ request()->routeIs('admin.dashboard') ? 'menu-item-active' : 'menu-item-inactive' }}">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V8.99998C3.25 10.2426 4.25736 11.25 5.5 11.25H9C10.2426 11.25 11.25 10.2426 11.25 8.99998V5.5C11.25 4.25736 10.2426 3.25 9 3.25H5.5ZM5.5 12.75C4.25736 12.75 3.25 13.7574 3.25 15V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H9C10.2426 20.75 11.25 19.7427 11.25 18.5V15C11.25 13.7574 10.2426 12.75 9 12.75H5.5ZM12.75 5.5C12.75 4.25736 13.7574 3.25 15 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V8.99998C20.75 10.2426 19.7426 11.25 18.5 11.25H15C13.7574 11.25 12.75 10.2426 12.75 8.99998V5.5ZM15 12.75C13.7574 12.75 12.75 13.7574 12.75 15V18.5C12.75 19.7426 13.7574 20.75 15 20.75H18.5C19.7426 20.75 20.75 19.7427 20.75 18.5V15C20.75 13.7574 19.7426 12.75 18.5 12.75H15Z" fill="currentColor"/></svg>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="flex">
                            <a href="{{ route('admin.quotations.index') }}"
                               class="menu-item {{ request()->routeIs('admin.quotations.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke="currentColor" stroke-width="1.6"/><path d="M14 2v6h6" stroke="currentColor" stroke-width="1.6"/><line x1="16" y1="13" x2="8" y2="13" stroke="currentColor" stroke-width="1.6"/><line x1="16" y1="17" x2="8" y2="17" stroke="currentColor" stroke-width="1.6"/></svg>
                                <span>Quotations</span>
                            </a>
                        </li>
                        <li class="flex">
                            <a href="{{ route('admin.services.index') }}"
                               class="menu-item {{ request()->routeIs('admin.services.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z" stroke="currentColor" stroke-width="1.6"/></svg>
                                <span>Lab Tests</span>
                            </a>
                        </li>
                        @if($currentAdmin?->role === 'master')
                        <li class="flex">
                            <a href="{{ route('admin.users.index') }}"
                               class="menu-item {{ request()->routeIs('admin.users.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="1.6"/><circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="1.6"/><path d="M23 21v-2a4 4 0 00-3-3.87" stroke="currentColor" stroke-width="1.6"/><path d="M16 3.13a4 4 0 010 7.75" stroke="currentColor" stroke-width="1.6"/></svg>
                                <span>Manage Users</span>
                            </a>
                        </li>
                        @endif
                        @if($currentAdmin?->role === 'master')
                        <li class="flex">
                            <a href="{{ route('admin.settings.index') }}"
                               class="menu-item {{ request()->routeIs('admin.settings.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.6"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 110 4h-.09a1.65 1.65 0 00-1.51 1z" stroke="currentColor" stroke-width="1.6"/></svg>
                                <span>Settings</span>
                            </a>
                        </li>
                        @endif
                        <li class="flex">
                            <a href="{{ route('admin.password.change') }}"
                               class="menu-item {{ request()->routeIs('admin.password.change*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="1.6"/></svg>
                                <span>Change Password</span>
                            </a>
                        </li>
                        <li class="flex">
                            <a href="{{ route('quotation.index') }}" target="_blank"
                               class="menu-item menu-item-inactive">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.6"/><line x1="2" y1="12" x2="22" y2="12" stroke="currentColor" stroke-width="1.6"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z" stroke="currentColor" stroke-width="1.6"/></svg>
                                <span>View Public Site</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- SIDEBAR LOGOUT (flush to bottom) -->
            <div class="mt-auto">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="menu-item menu-item-inactive w-full text-left">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" stroke="currentColor" stroke-width="1.6"/><polyline points="16 17 21 12 16 7" stroke="currentColor" stroke-width="1.6"/><line x1="21" y1="12" x2="9" y2="12" stroke="currentColor" stroke-width="1.6"/></svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Mobile backdrop -->
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-gray-900/50 xl:hidden"></div>

    <!-- ===== MAIN ===== -->
    <div class="flex flex-1 flex-col overflow-y-auto">
        <!-- TOPBAR -->
        <header class="sticky top-0 z-30 flex w-full items-center justify-between border-b border-gray-200 bg-white px-4 py-3.5 sm:px-6">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = !sidebarOpen" class="xl:hidden text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-gray-400">Home</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    <span class="font-medium text-gray-800">@yield('title', 'Dashboard')</span>
                </div>
            </div>

            <div class="flex items-center gap-3 sm:gap-4">
                @if(session('success'))
                <span class="hidden md:inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
                    {{ session('success') }}
                </span>
                @endif
                @if(session('error'))
                <span class="hidden md:inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-medium text-rose-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ session('error') }}
                </span>
                @endif

                <div class="flex items-center gap-2.5 pl-3 sm:pl-4 border-l border-gray-200">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#3c50e0] text-xs font-bold text-white">{{ strtoupper(substr(session('admin_name', 'A'), 0, 1)) }}</div>
                    <div class="hidden sm:block min-w-0">
                        <p class="truncate text-sm font-semibold leading-tight text-gray-800">{{ session('admin_name') }}</p>
                        <p class="truncate text-[11px] capitalize leading-tight text-gray-500">{{ session('admin_role') }}</p>
                    </div>
                </div>

            </div>
        </header>

        <!-- Flash messages (mobile) -->
        @if(session('success'))
        <div class="px-4 pt-4 xl:hidden">
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-medium text-emerald-700">{{ session('success') }}</div>
        </div>
        @endif
        @if(session('error'))
        <div class="px-4 pt-4 xl:hidden">
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-medium text-rose-700">{{ session('error') }}</div>
        </div>
        @endif

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@stack('scripts')
</body>
</html>
