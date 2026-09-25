@extends('admin.layouts.app')
@section('title', 'Invoices')
@section('styles')
<style>
    [x-cloak] { display: none !important; }
    #rowsBody tr td { transition: background-color .15s ease; }
    .tbl-loading tbody { opacity: .55; transition: opacity .15s ease; }
</style>
@endsection
@section('content')
<div x-data="invoicesList()" class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Final Invoices</h1>
            <p class="text-sm text-gray-500 mt-1"><span x-text="total"></span> total invoice{{ $invoices->total() !== 1 ? 's' : '' }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a :href="exportUrl('xlsx')" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export Excel
            </a>
            <a :href="exportUrl('csv')" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export CSV
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form @submit.prevent="onFilterChange" class="p-4 sm:p-5 border-b border-gray-100">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1.5">Search</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        </span>
                        <input type="text" id="search" x-model="filters.search" @input="onSearchInput"
                            placeholder="Search by #, client, company, email"
                            class="w-full h-[52px] rounded-xl border border-gray-300 pl-10 pr-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                    </div>
                </div>
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1.5">From</label>
                    <input type="date" id="date_from" x-model="filters.date_from" @change="onFilterChange"
                        class="w-full h-[52px] rounded-xl border border-gray-300 px-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition bg-white">
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1.5">To</label>
                    <input type="date" id="date_to" x-model="filters.date_to" @change="onFilterChange"
                        class="w-full h-[52px] rounded-xl border border-gray-300 px-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition bg-white">
                </div>
            </div>
            <div class="flex items-center gap-2 justify-end mt-3">
                <button type="button" @click="clearFilters"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Clear Filters
                </button>
            </div>
        </form>

        <!-- Toast -->
        <div x-show="toast.show" x-cloak x-transition
            :class="toast.type === 'error' ? 'bg-rose-50 border-rose-200 text-rose-700' : 'bg-emerald-50 border-emerald-200 text-emerald-700'"
            class="mx-4 mt-4 rounded-xl border px-4 py-3 text-sm flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path :class="toast.type === 'error' ? '' : 'hidden'" d="M12 9v4"/><path :class="toast.type === 'error' ? '' : 'hidden'" d="M12 17h.01"/>
                <path :class="toast.type === 'error' ? 'hidden' : ''" d="M22 11.08V12a10 10 0 11-5.93-9.14"/><path :class="toast.type === 'error' ? 'hidden' : ''" d="M22 4L12 14.01l-3-3"/>
                <circle class="hidden" cx="12" cy="12" r="10"/>
            </svg>
            <p class="font-medium" x-text="toast.message"></p>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-none border-none shadow-none overflow-hidden" :class="loading ? 'tbl-loading' : ''">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-gray-200 bg-gray-50/80 sticky top-0">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Invoice #</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Client</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Company</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Quotation</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Date</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Amount</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Status</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-600 whitespace-nowrap sticky right-0 bg-gray-50 border-l border-gray-200">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="rowsBody" class="divide-y divide-[rgb(229,229,229)]">
                        <tr>
                            <td colspan="8" class="px-3 py-12 text-center text-sm text-gray-400">Loading invoices...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="paginationWrap" class="px-4 py-3 border-t border-gray-100" @click="onPaginationClick($event)"></div>
        </div>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('invoicesList', () => ({
        dataUrl: '{{ route('admin.invoices.data') }}',
        exportBase: '{{ route('admin.export.invoices') }}',

        filters: {
            search: {{ Js::from($filters['search'] ?? '') }},
            date_from: {{ Js::from($filters['date_from'] ?? '') }},
            date_to: {{ Js::from($filters['date_to'] ?? '') }},
        },
        page: 1,
        loading: false,
        debounceTimer: null,
        requestSeq: 0,

        rowsHtml: '',
        paginationHtml: '',
        total: 0, from: 0, to: 0,
        currentPage: 1, lastPage: 1, hasPages: false,
        currentIds: [],

        toast: { show: false, type: 'success', message: '', timer: null },

        init() {
            this.total = {{ $invoices->total() }};
            this.fetchData();
        },

        exportUrl(format) {
            const params = new URLSearchParams();
            for (const [k, v] of Object.entries(this.filters)) {
                if (v !== '' && v != null) params.set(k, v);
            }
            params.set('format', format);
            return this.exportBase + '?' + params.toString();
        },

        onSearchInput() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => this.onFilterChange(), 350);
        },

        onFilterChange() {
            if (this.debounceTimer) clearTimeout(this.debounceTimer);
            if (this.page !== 1) this.page = 1;
            this.fetchData();
        },

        async fetchData() {
            const seq = ++this.requestSeq;
            this.loading = true;
            try {
                const params = new URLSearchParams();
                for (const [k, v] of Object.entries(this.filters)) {
                    if (v !== '' && v != null) params.set(k, v);
                }
                params.set('page', this.page);

                const res = await fetch(this.dataUrl + '?' + params.toString(), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) throw new Error((await res.json()).message || 'Failed to load invoices');
                const data = await res.json();
                if (seq !== this.requestSeq) return;

                this.rowsHtml = data.rows;
                this.paginationHtml = data.pagination;
                this.total = data.total; this.from = data.from; this.to = data.to;
                this.currentPage = data.current_page; this.lastPage = data.last_page; this.hasPages = data.has_pages;
                this.currentIds = (data.ids || []).map(String);

                this.$nextTick(() => {
                    document.getElementById('rowsBody').innerHTML = this.rowsHtml;
                    document.getElementById('paginationWrap').innerHTML = this.paginationHtml;
                });

                if (data.count === 0 && data.current_page > 1) {
                    this.page = data.current_page - 1;
                    this.fetchData();
                }
            } catch (e) {
                if (seq === this.requestSeq) this.showToast('error', e.message || 'Could not load invoices.');
            } finally {
                if (seq === this.requestSeq) this.loading = false;
            }
        },

        goToPage(p) {
            p = parseInt(p, 10);
            if (isNaN(p) || p < 1 || p > this.lastPage || p === this.currentPage) return;
            this.page = p;
            document.getElementById('rowsBody').scrollIntoView({ behavior: 'smooth', block: 'center' });
            this.fetchData();
        },

        onPaginationClick(e) {
            const btn = e.target.closest('[data-page]');
            if (!btn) return;
            this.goToPage(btn.getAttribute('data-page'));
        },

        clearFilters() {
            this.filters = { search: '', date_from: '', date_to: '' };
            if (this.debounceTimer) clearTimeout(this.debounceTimer);
            if (this.page !== 1) this.page = 1;
            this.fetchData();
        },

        showToast(type, message) {
            if (this.toast.timer) clearTimeout(this.toast.timer);
            this.toast = { show: true, type, message, timer: null };
            this.toast.timer = setTimeout(() => { this.toast.show = false; }, 4000);
        }
    }));
});
</script>
@endsection