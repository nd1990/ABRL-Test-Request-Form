@extends('admin.layouts.app')

@section('title', $invoice->invoice_number)

@section('content')
<div class="space-y-6 max-w-5xl">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.invoices.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $invoice->invoice_number }}</h1>
                <div class="flex items-center gap-2 mt-1">
                    @if($invoice->status === 'issued')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Issued</span>
                    @elseif($invoice->status === 'paid')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Paid</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 capitalize">{{ $invoice->status }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            @if($invoice->quotation && $currentAdmin?->hasPermission('quotations.view'))
            <a href="{{ route('admin.quotations.show', $invoice->quotation) }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                View Quotation
            </a>
            @endif
            <a href="{{ route('admin.invoices.pdf', $invoice) }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Download PDF
            </a>
            @if($currentAdmin?->hasPermission('invoices.delete'))
            <form method="POST" action="{{ route('admin.invoices.destroy', $invoice) }}" class="inline" x-data onsubmit="return confirm('Delete this invoice? Its parameters will be available for re-invoicing.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-rose-700 bg-rose-50 border border-rose-200 rounded-lg hover:bg-rose-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                    Delete Invoice
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Billed To</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Name</p>
                        <p class="text-sm font-medium text-gray-900">{{ $invoice->client_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Company</p>
                        <p class="text-sm font-medium text-gray-900">{{ $invoice->company_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Email</p>
                        <p class="text-sm font-medium text-gray-900">{{ $invoice->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Phone</p>
                        <p class="text-sm font-medium text-gray-900">{{ $invoice->phone ?: '—' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-500">Address</p>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $invoice->address ?: '' }}{{ $invoice->address_line2 ? ', ' . $invoice->address_line2 : '' }}{{ $invoice->city ? ', ' . $invoice->city : '' }}{{ $invoice->state ? ', ' . $invoice->state : '' }}{{ $invoice->postal_code ? ', ' . $invoice->postal_code : '' }}{{ $invoice->country ? ', ' . $invoice->country : '' }}
                            @if(!$invoice->address && !$invoice->city && !$invoice->state && !$invoice->country && !$invoice->postal_code) — @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">GST Number</p>
                        <p class="text-sm font-medium text-gray-900">{{ $invoice->gst_number ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">PAN Number</p>
                        <p class="text-sm font-medium text-gray-900">{{ $invoice->pan_number ?: '—' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Invoiced Parameters ({{ $invoice->items->count() }})</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left px-4 py-2.5 font-semibold text-gray-600">Service / Parameter</th>
                                <th class="text-left px-4 py-2.5 font-semibold text-gray-600">Description</th>
                                <th class="text-center px-4 py-2.5 font-semibold text-gray-600">Qty</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-600">Unit Price</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-600">Discount</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-600">Tax %</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-600">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($invoice->items as $item)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $item->service_name_snapshot }}</td>
                                <td class="px-4 py-3 text-gray-600 whitespace-pre-line">{{ $item->description_snapshot }}</td>
                                <td class="px-4 py-3 text-center text-gray-800">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-right text-gray-800">{{ $currency }}{{ number_format($item->unit_price_snapshot, 2) }}</td>
                                <td class="px-4 py-3 text-right text-gray-800">{{ $currency }}{{ number_format($item->discount_snapshot, 2) }}</td>
                                <td class="px-4 py-3 text-right text-gray-800">{{ number_format($item->tax_percentage_snapshot, 1) }}%</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900">{{ $currency }}{{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                    <div class="flex justify-end">
                        <div class="w-72 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Subtotal</span>
                                <span class="font-medium text-gray-900">{{ $currency }}{{ number_format($invoice->subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Discount</span>
                                <span class="font-medium text-gray-900">-{{ $currency }}{{ number_format($invoice->discount, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Tax</span>
                                <span class="font-medium text-gray-900">{{ $currency }}{{ number_format($invoice->tax, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-base font-bold pt-2 border-t border-gray-200">
                                <span class="text-gray-900">Grand Total</span>
                                <span class="text-brand-600">{{ $currency }}{{ number_format($invoice->grand_total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Invoice Details</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500">Invoice Date</p>
                        <p class="text-sm font-medium text-gray-900">{{ $invoice->invoice_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Source Quotation</p>
                        <p class="text-sm font-medium text-gray-900">
                            @if($invoice->quotation)
                                <a href="{{ route('admin.quotations.show', $invoice->quotation) }}" class="text-brand-600 hover:underline">{{ $invoice->quotation->quotation_number }}</a>
                            @else
                                —
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Created By</p>
                        <p class="text-sm font-medium text-gray-900">{{ $invoice->creator->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Status</p>
                        <p class="text-sm font-medium text-gray-900 capitalize">{{ $invoice->status }}</p>
                    </div>
                </div>
            </div>

            @if($invoice->notes)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Notes</h2>
                <p class="text-sm text-gray-900 whitespace-pre-line">{{ $invoice->notes }}</p>
            </div>
            @endif
        </div>
    </div>

</div>
@endsection