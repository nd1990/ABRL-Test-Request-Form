@extends('admin.layouts.app')

@section('title', $quotation->quotation_number)

@section('content')
<div class="space-y-6 max-w-5xl">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.quotations.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $quotation->quotation_number }}</h1>
                <div class="flex items-center gap-2 mt-1">
                    @if($quotation->status === 'sent')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Sent</span>
                    @elseif($quotation->status === 'accepted')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Accepted</span>
                    @elseif($quotation->status === 'rejected')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">Rejected</span>
                    @elseif($quotation->status === 'cancelled')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Cancelled</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Draft</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.quotations.edit', $quotation) }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit
            </a>
            <a href="{{ route('admin.quotations.pdf', $quotation) }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Download PDF
            </a>
            <form method="POST" action="{{ route('admin.quotations.resend', $quotation) }}" class="inline" x-data>
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 014-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 01-4 4H3"/></svg>
                    Resend Email
                </button>
            </form>
            <form method="POST" action="{{ route('admin.quotations.duplicate', $quotation) }}" class="inline" x-data>
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-violet-700 bg-violet-50 border border-violet-200 rounded-lg hover:bg-violet-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                    Duplicate
                </button>
            </form>
            <form method="POST" action="{{ route('admin.quotations.destroy', $quotation) }}" class="inline" x-data onsubmit="return confirm('Are you sure you want to permanently delete this quotation?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-rose-700 bg-rose-50 border border-rose-200 rounded-lg hover:bg-rose-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Client Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Name</p>
                        <p class="text-sm font-medium text-gray-900">{{ $quotation->client_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Company</p>
                        <p class="text-sm font-medium text-gray-900">{{ $quotation->company_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Email</p>
                        <p class="text-sm font-medium text-gray-900">{{ $quotation->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Phone</p>
                        <p class="text-sm font-medium text-gray-900">{{ $quotation->phone ?: '—' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-500">Address</p>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $quotation->address ?: '' }}{{ $quotation->address_line2 ? ', ' . $quotation->address_line2 : '' }}{{ $quotation->city ? ', ' . $quotation->city : '' }}{{ $quotation->state ? ', ' . $quotation->state : '' }}{{ $quotation->postal_code ? ', ' . $quotation->postal_code : '' }}{{ $quotation->country ? ', ' . $quotation->country : '' }}
                            @if(!$quotation->address && !$quotation->city && !$quotation->state && !$quotation->country && !$quotation->postal_code) — @endif
                        </p>
                    </div>
                    @if($quotation->courier_address)
                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-500">Courier Address</p>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $quotation->courier_address }}{{ $quotation->courier_address_line2 ? ', ' . $quotation->courier_address_line2 : '' }}{{ $quotation->courier_city ? ', ' . $quotation->courier_city : '' }}{{ $quotation->courier_state ? ', ' . $quotation->courier_state : '' }}{{ $quotation->courier_postal_code ? ', ' . $quotation->courier_postal_code : '' }}
                        </p>
                    </div>
                    @endif
                    <div>
                        <p class="text-xs text-gray-500">GST Number</p>
                        <p class="text-sm font-medium text-gray-900">{{ $quotation->gst_number ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">PAN Number</p>
                        <p class="text-sm font-medium text-gray-900">{{ $quotation->pan_number ?: '—' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Line Items</h2>
                </div>
                <div class="overflow-x-auto">
                    @php
                        $labItems = $quotation->items->filter(fn($i) => empty($i->service_id));
                        $pricedItems = $quotation->items->filter(fn($i) => !empty($i->service_id));
                        $isLabOnly = $labItems->isNotEmpty() && $pricedItems->isEmpty();
                    @endphp
                    @if($isLabOnly)
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left px-4 py-2.5 font-semibold text-gray-600">Test / Parameter</th>
                                <th class="text-left px-4 py-2.5 font-semibold text-gray-600">Method</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-600">No. of Samples</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($quotation->items as $item)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $item->service_name_snapshot }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $item->unit_snapshot }}</td>
                                <td class="px-4 py-3 text-right text-gray-800">{{ $item->quantity }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left px-4 py-2.5 font-semibold text-gray-600">Service</th>
                                <th class="text-left px-4 py-2.5 font-semibold text-gray-600">Description</th>
                                <th class="text-center px-4 py-2.5 font-semibold text-gray-600">Qty</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-600">Unit Price</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-600">Discount</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-600">Tax %</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-600">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($quotation->items as $item)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $item->service_name_snapshot }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $item->description_snapshot }}</td>
                                <td class="px-4 py-3 text-center text-gray-800">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-right text-gray-800">{{ $currency }}{{ number_format($item->unit_price_snapshot, 2) }}</td>
                                <td class="px-4 py-3 text-right text-gray-800">{{ $currency }}{{ number_format($item->discount_snapshot, 2) }}</td>
                                <td class="px-4 py-3 text-right text-gray-800">{{ number_format($item->tax_percentage_snapshot, 1) }}%</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900">{{ $currency }}{{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>

                @unless($isLabOnly ?? false)
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                    <div class="flex justify-end">
                        <div class="w-72 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Subtotal</span>
                                <span class="font-medium text-gray-900">{{ $currency }}{{ number_format($quotation->subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Discount</span>
                                <span class="font-medium text-gray-900">-{{ $currency }}{{ number_format($quotation->discount, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Tax</span>
                                <span class="font-medium text-gray-900">{{ $currency }}{{ number_format($quotation->tax, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-base font-bold pt-2 border-t border-gray-200">
                                <span class="text-gray-900">Grand Total</span>
                                <span class="text-brand-600">{{ $currency }}{{ number_format($quotation->grand_total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endunless
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Quotation Details</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500">Date</p>
                        <p class="text-sm font-medium text-gray-900">{{ $quotation->quotation_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Valid Until</p>
                        <p class="text-sm font-medium text-gray-900">{{ $quotation->valid_until ? $quotation->valid_until->format('d M Y') : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Created By</p>
                        <p class="text-sm font-medium text-gray-900">{{ $quotation->creator->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Email Status</p>
                        @if($quotation->email_status === 'sent')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Sent</span>
                        @elseif($quotation->email_status === 'failed')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">Failed</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Pending</span>
                        @endif
                    </div>
                </div>
            </div>

            @if($quotation->project_description || $quotation->additional_requirements || $quotation->notes || $quotation->expected_timeline || $quotation->preferred_contact_method)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Additional Requirements</h2>
                <div class="space-y-3">
                    @if($quotation->project_description)
                    <div>
                        <p class="text-xs text-gray-500">Project Description</p>
                        <p class="text-sm text-gray-900 mt-1 whitespace-pre-line">{{ $quotation->project_description }}</p>
                    </div>
                    @endif
                    @if($quotation->additional_requirements)
                    <div>
                        <p class="text-xs text-gray-500">Additional Requirements</p>
                        <p class="text-sm text-gray-900 mt-1 whitespace-pre-line">{{ $quotation->additional_requirements }}</p>
                    </div>
                    @endif
                    @if($quotation->notes)
                    <div>
                        <p class="text-xs text-gray-500">Notes</p>
                        <p class="text-sm text-gray-900 mt-1 whitespace-pre-line">{{ $quotation->notes }}</p>
                    </div>
                    @endif
                    @if($quotation->expected_timeline)
                    <div>
                        <p class="text-xs text-gray-500">Expected Timeline</p>
                        <p class="text-sm text-gray-900 mt-1">{{ $quotation->expected_timeline }}</p>
                    </div>
                    @endif
                    @if($quotation->preferred_contact_method)
                    <div>
                        <p class="text-xs text-gray-500">Preferred Contact Method</p>
                        <p class="text-sm text-gray-900 mt-1 capitalize">{{ $quotation->preferred_contact_method }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

</div>
@endsection
