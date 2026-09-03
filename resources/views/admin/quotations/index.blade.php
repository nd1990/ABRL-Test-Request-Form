@extends('admin.layouts.app')

@section('title', 'Quotations')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Quotations</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $quotations->total() }} total quotation{{ $quotations->total() !== 1 ? 's' : '' }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.export.quotations', array_merge($filters, ['format' => 'xlsx'])) }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export Excel
            </a>
            <a href="{{ route('admin.export.quotations', array_merge($filters, ['format' => 'csv'])) }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export CSV
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form method="GET" action="{{ route('admin.quotations.index') }}" class="p-4 sm:p-5 border-b border-gray-100">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div class="lg:col-span-2">
                    <label for="search" class="sr-only">Search</label>
                    <input type="text" id="search" name="search" value="{{ $filters['search'] ?? '' }}"
                           placeholder="Search by #, client, company, email"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                </div>
                <div>
                    <label for="date_from" class="sr-only">From</label>
                    <input type="date" id="date_from" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                </div>
                <div>
                    <label for="date_to" class="sr-only">To</label>
                    <input type="date" id="date_to" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                </div>
                <div class="flex gap-2">
                    <select name="status" class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none bg-white">
                        <option value="">All Status</option>
                        <option value="draft" {{ ($filters['status'] ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="sent" {{ ($filters['status'] ?? '') === 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="accepted" {{ ($filters['status'] ?? '') === 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="rejected" {{ ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 mt-3">
                <div class="lg:col-span-2">
                    <select name="email_status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none bg-white">
                        <option value="">All Email Status</option>
                        <option value="sent" {{ ($filters['email_status'] ?? '') === 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="failed" {{ ($filters['email_status'] ?? '') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="pending" {{ ($filters['email_status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div class="lg:col-span-3 flex items-center gap-2 justify-end">
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition">
                        Apply Filters
                    </button>
                    <a href="{{ route('admin.quotations.index') }}"
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        @if($quotations->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Quotation #</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Client</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Company</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Email</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Date</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Amount</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Email</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600 whitespace-nowrap sticky right-0 bg-gray-50 border-l border-gray-200">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[rgb(229,229,229)]">
                    @foreach($quotations as $q)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 font-medium text-brand-600 whitespace-nowrap">
                            <a href="{{ route('admin.quotations.show', $q) }}" class="hover:underline">{{ $q->quotation_number }}</a>
                        </td>
                        <td class="px-4 py-3 text-gray-800 whitespace-nowrap">{{ $q->client_name }}</td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $q->company_name }}</td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $q->email }}</td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $q->quotation_date->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-gray-800 text-right font-medium whitespace-nowrap">{{ number_format($q->grand_total, 2) }}</td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            @if($q->email_status === 'sent')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Sent</span>
                            @elseif($q->email_status === 'failed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">Failed</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Pending</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap sticky right-0 bg-white border-l border-gray-200">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.quotations.show', $q) }}"
                                   class="p-1.5 text-gray-500 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition" title="View">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <a href="{{ route('admin.quotations.edit', $q) }}"
                                   class="p-1.5 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                <a href="{{ route('admin.quotations.pdf', $q) }}"
                                   class="p-1.5 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Download PDF">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                </a>
                                <form method="POST" action="{{ route('admin.quotations.destroy', $q) }}" class="inline"
                                      x-data onsubmit="return confirm('Are you sure you want to permanently delete this quotation?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="p-1.5 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.quotations.resend', $q) }}" class="inline" x-data>
                                    @csrf
                                    <button type="submit"
                                            class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Resend Email">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 014-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 01-4 4H3"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-gray-100">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-sm text-gray-500">
                    Showing {{ $quotations->firstItem() }} to {{ $quotations->lastItem() }} of {{ $quotations->total() }} results
                </p>
                <div class="flex items-center gap-1">
                    @if($quotations->onFirstPage())
                        <span class="px-3 py-1.5 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed border border-gray-200">Previous</span>
                    @else
                        <a href="{{ $quotations->previousPageUrl() . '&' . http_build_query($filters) }}"
                           class="px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Previous</a>
                    @endif

                    @foreach($quotations->getUrlRange(max(1, $quotations->currentPage() - 2), min($quotations->lastPage(), $quotations->currentPage() + 2)) as $page => $url)
                        <a href="{{ $url . '&' . http_build_query($filters) }}"
                           class="px-3 py-1.5 text-sm rounded-lg border transition {{ $page === $quotations->currentPage() ? 'bg-brand-600 text-white border-brand-600' : 'text-gray-700 bg-white border-gray-300 hover:bg-gray-50' }}">
                            {{ $page }}
                        </a>
                    @endforeach

                    @if($quotations->hasMorePages())
                        <a href="{{ $quotations->nextPageUrl() . '&' . http_build_query($filters) }}"
                           class="px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Next</a>
                    @else
                        <span class="px-3 py-1.5 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed border border-gray-200">Next</span>
                    @endif
                </div>
            </div>
        </div>

        @else
        <div class="px-6 py-16 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            <h3 class="mt-3 text-lg font-semibold text-gray-900">No quotations found</h3>
            <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or create a new quotation.</p>
            @if(collect($filters)->filter()->isNotEmpty())
            <a href="{{ route('admin.quotations.index') }}" class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-brand-600 bg-brand-50 border border-brand-200 rounded-lg hover:bg-brand-100 transition">
                Clear Filters
            </a>
            @endif
        </div>
        @endif
    </div>

</div>
@endsection
