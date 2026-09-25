@extends('admin.layouts.app')
@section('title', 'Lab Tests')
@section('styles')
<style>
    [x-cloak] { display: none !important; }
    #rowsBody tr td { transition: background-color .15s ease; }
    .tbl-loading tbody { opacity: .55; transition: opacity .15s ease; }
    .checkbox-indeterminate { accent-color: #01458e; }
</style>
@endsection
@section('content')
<div x-data="servicesList()" class="space-y-6">

{{-- Gate config for Alpine --}}
<input type="hidden" id="canDeleteLabTests" value="{{ $currentAdmin?->hasPermission('lab_tests.delete') ? '1' : '0' }}">
<input type="hidden" id="canCreateLabTests" value="{{ $currentAdmin?->hasPermission('lab_tests.create') ? '1' : '0' }}">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Lab Tests</h1>
            <p class="text-sm text-gray-500 mt-1">Manage the NABL catalog used by the public quotation form</p>
        </div>
        <div class="flex items-center gap-3">
            @if($currentAdmin?->hasPermission('lab_tests.create'))
            <button @click="openImport()" class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Import CSV
            </button>
            <button @click="openAdd()" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-lg shadow-brand-500/25 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Lab Test
            </button>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-4">
            <div class="lg:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1.5">Search</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </span>
                    <input type="text" id="search" x-model="filters.search" @input="onSearchInput"
                        placeholder="S.No, parameter, material, method, protocol..."
                        class="w-full rounded-xl border border-gray-300 pl-10 pr-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                </div>
            </div>
            <div>
                <label for="nabl_type" class="block text-sm font-medium text-gray-700 mb-1.5">NABL Type</label>
                <select id="nabl_type" x-model="filters.nabl_type" @change="onFilterChange"
                    class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition bg-white">
                    <option value="">All</option>
                    @foreach($nablTypes as $value)
                    <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="discipline" class="block text-sm font-medium text-gray-700 mb-1.5">Discipline</label>
                <select id="discipline" x-model="filters.discipline" @change="onFilterChange"
                    class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition bg-white">
                    <option value="">All</option>
                    @foreach($disciplines as $value)
                    <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="material" class="block text-sm font-medium text-gray-700 mb-1.5">Material</label>
                <select id="material" x-model="filters.material" @change="onFilterChange"
                    class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition bg-white">
                    <option value="">All</option>
                    @foreach($materials as $value)
                    <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="parameter" class="block text-sm font-medium text-gray-700 mb-1.5">Parameter</label>
                <select id="parameter" x-model="filters.parameter" @change="onFilterChange"
                    class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition bg-white">
                    <option value="">All</option>
                    @foreach($parameters as $value)
                    <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                <select id="status" x-model="filters.status" @change="onFilterChange"
                    class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition bg-white">
                    <option value="">All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
        <div class="mt-4 flex items-center gap-3">
            <button type="button" @click="onFilterChange()"
                class="inline-flex items-center gap-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Filter
            </button>
            <button type="button" @click="clearFilters()"
                class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                Clear
            </button>
        </div>
    </div>

    <!-- Bulk action bar -->
    <div x-show="canDelete && selectedCount > 0" x-cloak x-transition
        class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-brand-50 border border-brand-200 rounded-2xl px-4 py-3 sm:px-6">
        <div class="flex items-center gap-3">
            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-600 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
            </span>
            <p class="text-sm font-semibold text-brand-900"><span x-text="selectedCount"></span> selected</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" @click="clearSelection"
                class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition">
                Clear selection
            </button>
            <button type="button" @click="requestBulkDelete" :disabled="deleting"
                class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 disabled:opacity-50 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition">
                <span x-show="!deleting">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                </span>
                <span x-show="deleting" class="w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin"></span>
                <span x-text="deleting ? 'Deleting...' : 'Delete ' + selectedCount + ' Selected'"></span>
            </button>
        </div>
    </div>

    <!-- Toast -->
    <div x-show="toast.show" x-cloak x-transition
        :class="toast.type === 'error' ? 'bg-rose-50 border-rose-200 text-rose-700' : 'bg-emerald-50 border-emerald-200 text-emerald-700'"
        class="rounded-xl border px-4 py-3 text-sm flex items-start gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path :class="toast.type === 'error' ? '' : 'hidden'" d="M12 9v4"/><path :class="toast.type === 'error' ? '' : 'hidden'" d="M12 17h.01"/>
            <path :class="toast.type === 'error' ? 'hidden' : ''" d="M22 11.08V12a10 10 0 11-5.93-9.14"/><path :class="toast.type === 'error' ? 'hidden' : ''" d="M22 4L12 14.01l-3-3"/>
            <circle class="hidden" cx="12" cy="12" r="10"/>
        </svg>
        <p class="font-medium" x-text="toast.message"></p>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden" :class="loading ? 'tbl-loading' : ''">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/80">
                        @if($currentAdmin?->hasPermission('lab_tests.delete'))
                        <th class="w-10 px-2 py-3">
                            <input type="checkbox" id="selectAll" data-select-all
                                class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500 cursor-pointer"
                                :checked="selectAllChecked"
                                :indeterminate="selectAllIndeterminate"
                                @change="toggleSelectAll($event)">
                        </th>
                        @endif
                        <th class="text-left font-semibold text-gray-500 px-2 py-3">S.No</th>
                        <th class="text-left font-semibold text-gray-500 px-2 py-3">NABL</th>
                        <th class="text-left font-semibold text-gray-500 px-2 py-3">Discipline</th>
                        <th class="text-left font-semibold text-gray-500 px-2 py-3">Material</th>
                        <th class="text-left font-semibold text-gray-500 px-2 py-3">Parameter</th>
                        <th class="text-left font-semibold text-gray-500 px-2 py-3">Method</th>
                        <th class="text-left font-semibold text-gray-500 px-2 py-3">Sample Qty</th>
                        <th class="text-right font-semibold text-gray-500 px-2 py-3">Charges/Sample</th>
                        <th class="text-right font-semibold text-gray-500 px-2 py-3">Total</th>
                        <th class="text-center font-semibold text-gray-500 px-2 py-3">Status</th>
                        @if($currentAdmin?->hasAnyPermission(['lab_tests.edit', 'lab_tests.delete']))
                        <th class="text-right font-semibold text-gray-500 px-2 py-3">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="rowsBody" class="divide-y divide-gray-100" @click="onRowClick($event)" @change="onRowChange($event)">
                    <tr>
                        <td colspan="11" class="px-3 py-12 text-center text-sm text-gray-400">Loading services...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="paginationWrap" class="px-6 py-4 border-t border-gray-100" @click="onPaginationClick($event)"></div>
    </div>

    @include('admin.lab-tests._modal')

    <!-- Import CSV Modal -->
    <div x-show="importOpen" x-cloak x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="closeImport()"></div>
        <div x-show="importOpen" x-cloak x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto" style="display:none;">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-2xl flex items-center justify-between z-10">
                <h3 class="text-lg font-bold text-gray-900">Import Lab Tests from CSV</h3>
                <button @click="closeImport()" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.services.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf

                <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0 mt-0.5 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <p class="text-sm text-amber-800">
                            Existing lab tests <span class="font-semibold">will remain unchanged</span>. Only new tests (not already present with the same NABL type, Discipline, Material, and Parameter) will be added from the CSV.
                        </p>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-xs text-gray-600 space-y-1">
                    <p class="font-semibold text-gray-800 mb-1">Expected column headers (auto-detected):</p>
                    <p>S.No, NABL/NON NABL, Discipline, Materials or Products, Parameter, Method, Sample Quantity, Lead time, Charges per sample, GST SAC, Total amount, PROTOCOL NO, NABL Range, Limit of quantification, Minimum sample handeling, Protocol Link</p>
                </div>

                <div>
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-1.5">CSV File <span class="text-rose-500">*</span></label>
                    <input type="file" id="file" name="file" accept=".csv,.txt" required
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <button type="button" @click="closeImport()"
                        class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold px-5 py-2.5 rounded-xl transition">
                        Cancel
                    </button>
                    <button type="submit" :disabled="importing"
                        class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-lg shadow-brand-500/25 transition">
                        <span x-show="importing" class="w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin"></span>
                        <span x-text="importing ? 'Importing...' : 'Import CSV'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('servicesList', () => ({
        dataUrl: '{{ route('admin.services.data') }}',
        bulkUrl: '{{ route('admin.services.bulk-destroy') }}',
        destroyUrlBase: '{{ route('admin.services.destroy', '__ID__') }}'.replace('__ID__', ''),

        filters: {
            search: {{ Js::from($filters['search'] ?? '') }},
            nabl_type: {{ Js::from($filters['nabl_type'] ?? '') }},
            discipline: {{ Js::from($filters['discipline'] ?? '') }},
            material: {{ Js::from($filters['material'] ?? '') }},
            parameter: {{ Js::from($filters['parameter'] ?? '') }},
            status: {{ Js::from($filters['status'] ?? '') }},
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

        selected: [],
        deleting: false,
        canDelete: document.getElementById('canDeleteLabTests')?.value === '1',
        canCreate: document.getElementById('canCreateLabTests')?.value === '1',
        toast: { show: false, type: 'success', message: '', timer: null },

        // ------- Modal (delegated from _modal partial) -------
        modalOpen: false,
        modalAction: '{{ route('admin.services.store') }}',
        modalMethod: 'POST',
        modalTitle: 'Add Lab Test',
        current: { id:'', s_no:'', nabl_type:'NABL', discipline:'', material:'', parameter:'', method:'', sample_quantity:'', lead_time:'', charges_per_sample:'', gst_amount:'', total_amount:'', protocol_no:'', nabl_range:'', limit_of_quantification:'', remarks:'', is_active:true },

        importOpen: false,
        importing: false,

        init() {
            this.fetchData();
        },

        get selectedIds() { return this.selected.map(String); },
        get selectedCount() { return this.selected.length; },
        get selectAllChecked() { return this.currentIds.length > 0 && this.currentIds.every(id => this.selected.includes(String(id))); },
        get selectAllIndeterminate() { return !this.selectAllChecked && this.currentIds.some(id => this.selected.includes(String(id))); },

        onSearchInput() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => this.onFilterChange(), 350);
        },

        onFilterChange() {
            this.clearTimeout();
            if (this.page !== 1) this.page = 1;
            this.clearSelection();
            this.fetchData();
        },

        clearTimeout() { if (this.debounceTimer) { clearTimeout(this.debounceTimer); this.debounceTimer = null; } },

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
                if (!res.ok) throw new Error((await res.json()).message || 'Failed to load services');
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
                    this.syncChecks();
                });

                // If current page became empty and we're past page 1, step back one page.
                if (data.count === 0 && data.current_page > 1) {
                    this.page = data.current_page - 1;
                    this.fetchData();
                }
            } catch (e) {
                if (seq === this.requestSeq) this.showToast('error', e.message || 'Could not load services.');
            } finally {
                if (seq === this.requestSeq) this.loading = false;
            }
        },

        goToPage(p) {
            p = parseInt(p, 10);
            if (isNaN(p) || p < 1 || p > this.lastPage || p === this.currentPage) return;
            this.page = p;
            this.clearSelection();
            document.getElementById('rowsBody').scrollIntoView({ behavior: 'smooth', block: 'center' });
            this.fetchData();
        },

        onPaginationClick(e) {
            const btn = e.target.closest('[data-page]');
            if (!btn) return;
            this.goToPage(btn.getAttribute('data-page'));
        },

        // ------- Selection -------
        onRowChange(e) {
            const cb = e.target.closest('[data-check]');
            if (!cb) return;
            const id = String(cb.value);
            if (cb.checked) { if (!this.selected.includes(id)) this.selected.push(id); }
            else { this.selected = this.selected.filter(x => x !== id); }
            this.syncChecks();
        },

        toggleSelectAll(e) {
            const checked = e.target.checked;
            if (checked) {
                const set = new Set(this.selected.map(String));
                this.currentIds.forEach(id => set.add(id));
                this.selected = [...set];
            } else {
                const drop = new Set(this.currentIds);
                this.selected = this.selected.filter(x => !drop.has(x));
            }
            this.syncChecks();
        },

        syncChecks() {
            const checked = new Set(this.selected.map(String));
            document.querySelectorAll('#rowsBody [data-check]').forEach(cb => {
                cb.checked = checked.has(String(cb.value));
            });
        },

        clearSelection() { this.selected = []; this.syncChecks(); },

        // ------- Row actions (delegated) -------
        onRowClick(e) {
            const edit = e.target.closest('[data-action="edit"]');
            if (edit) {
                try { const obj = JSON.parse(edit.getAttribute('data-payload')); this.openEdit(obj); } catch (_) {}
                return;
            }
            const del = e.target.closest('[data-action="delete"]');
            if (del) this.singleDelete(del.getAttribute('data-id'));
        },

        async singleDelete(id) {
            if (!this.canDelete) return;
            if (!confirm('Are you sure you want to delete this service?')) return;
            try {
                const res = await fetch(this.destroyUrlBase + id, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Could not delete the service.');
                this.showToast('success', data.message || 'Service deleted.');
                this.selected = this.selected.filter(x => x !== String(id));
                this.fetchData();
            } catch (e) {
                this.showToast('error', e.message || 'Could not delete the service.');
            }
        },

        requestBulkDelete() {
            if (!this.canDelete) return;
            if (this.selected.length === 0 || this.deleting) return;
            if (!confirm('Are you sure you want to delete the selected services?')) return;
            this.bulkDelete();
        },

        async bulkDelete() {
            this.deleting = true;
            try {
                const res = await fetch(this.bulkUrl, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ ids: this.selected.map(Number) })
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || data.errors || 'Could not delete the selected services.');
                this.selected = [];
                this.showToast('success', data.message || 'Selected services deleted.');
                this.fetchData();
            } catch (e) {
                this.showToast('error', e.message || 'Could not delete the selected services.');
            } finally {
                this.deleting = false;
            }
        },

        // ------- Modal helpers -------
        openAdd() {
            this.modalAction = '{{ route('admin.services.store') }}';
            this.modalMethod = 'POST';
            this.modalTitle = 'Add Lab Test';
            this.current = { id:'', s_no:'', nabl_type:'NABL', discipline:'', material:'', parameter:'', method:'', sample_quantity:'', lead_time:'', charges_per_sample:'', gst_amount:'', total_amount:'', protocol_no:'', nabl_range:'', limit_of_quantification:'', remarks:'', is_active:true };
            this.modalOpen = true;
        },
        openEdit(t) {
            this.modalAction = '{{ route('admin.services.update', '__ID__') }}'.replace('__ID__', t.id);
            this.modalMethod = 'PUT';
            this.modalTitle = 'Edit Lab Test';
            this.current = {
                id:t.id, s_no:t.s_no || '', nabl_type:t.nabl_type, discipline:t.discipline,
                material:t.material || '', parameter:t.parameter, method:t.method || '',
                sample_quantity:t.sample_quantity || '', lead_time:t.lead_time || '',
                charges_per_sample:t.charges_per_sample || '', gst_amount:t.gst_amount || '', total_amount:t.total_amount || '',
                protocol_no:t.protocol_no || '', nabl_range:t.nabl_range || '',
                limit_of_quantification:t.limit_of_quantification || '', remarks:t.remarks || '',
                is_active:!!t.is_active
            };
            this.modalOpen = true;
        },
        closeModal() { this.modalOpen = false; },

        openImport() { this.importOpen = true; },
        closeImport() { this.importOpen = false; },

        clearFilters() {
            this.filters = { search:'', nabl_type:'', discipline:'', material:'', parameter:'', status:'' };
            this.clearTimeout();
            if (this.page !== 1) this.page = 1;
            this.clearSelection();
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
