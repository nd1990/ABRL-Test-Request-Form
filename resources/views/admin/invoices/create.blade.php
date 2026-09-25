@extends('admin.layouts.app')

@section('title', 'Generate Invoice')

@section('content')
<div class="space-y-6 max-w-5xl" x-data="invoiceGenerator()">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.quotations.show', $quotation) }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Generate Final Invoice</h1>
            <p class="text-sm text-gray-500 mt-1">From quotation <a href="{{ route('admin.quotations.show', $quotation) }}" class="text-brand-600 hover:underline">{{ $quotation->quotation_number }}</a></p>
        </div>
    </div>

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

    <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-4 py-3 text-sm flex items-start gap-2.5">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <p class="flex-1">
            Select only the parameters that were actually executed. Parameters you leave <strong>unchecked</strong> will be excluded from this final invoice but will remain linked to the original quotation for record purposes.
        </p>
    </div>

    <form method="POST" action="{{ route('admin.invoices.store', $quotation) }}">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Select Parameters to Invoice</h2>
                <span class="text-xs text-gray-500"><span x-text="selectedCount" class="font-semibold text-brand-600"></span> / {{ $eligibleItems->count() }} selected</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-4 py-2.5 font-semibold text-gray-600 w-12">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" x-model="allChecked" @change="toggleAll()" class="w-4 h-4 text-brand-600 border-gray-300 rounded focus:ring-brand-500">
                                </label>
                            </th>
                            <th class="text-left px-4 py-2.5 font-semibold text-gray-600">Parameter / Service</th>
                            <th class="text-left px-4 py-2.5 font-semibold text-gray-600">Details</th>
                            <th class="text-center px-4 py-2.5 font-semibold text-gray-600">Qty</th>
                            <th class="text-right px-4 py-2.5 font-semibold text-gray-600">Unit Price</th>
                            <th class="text-right px-4 py-2.5 font-semibold text-gray-600">Tax %</th>
                            <th class="text-right px-4 py-2.5 font-semibold text-gray-600">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="item in items" :key="item.id">
                            <tr class="hover:bg-gray-50/50" :class="!isSelected(item.id) ? 'opacity-50' : ''">
                                <td class="px-4 py-3">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="items[]" :value="item.id" @change="onToggle($event, item.id)"
                                               :checked="isSelected(item.id)"
                                               class="w-4 h-4 text-brand-600 border-gray-300 rounded focus:ring-brand-500">
                                    </label>
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900" x-text="item.name"></td>
                                <td class="px-4 py-3 text-gray-600" x-text="item.details"></td>
                                <td class="px-4 py-3 text-center text-gray-800" x-text="item.quantity"></td>
                                <td class="px-4 py-3 text-right text-gray-800" x-text="currency + item.unit_price.toFixed(2)"></td>
                                <td class="px-4 py-3 text-right text-gray-800" x-text="item.tax_percentage.toFixed(1) + '%'"></td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900" x-text="currency + item.total.toFixed(2)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                <div class="flex justify-end">
                    <div class="w-72 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Selected subtotal</span>
                            <span class="font-medium text-gray-900" x-text="currency + totals.subtotal.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Discount</span>
                            <span class="font-medium text-gray-900" x-text="'-' + currency + totals.discount.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Tax</span>
                            <span class="font-medium text-gray-900" x-text="currency + totals.tax.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-base font-bold pt-2 border-t border-gray-200">
                            <span class="text-gray-900">Invoice Total</span>
                            <span class="text-brand-600" x-text="currency + totals.grand_total.toFixed(2)"></span>
                        </div>
                        <p class="text-xs text-gray-400 text-right">Original quotation total: {{ $currency }}{{ number_format($quotation->grand_total, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.quotations.show', $quotation) }}"
               class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit"
                    :disabled="selectedCount === 0"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition disabled:opacity-40 disabled:cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
                Generate Final Invoice
            </button>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
function invoiceGenerator() {
    return {
        currency: @js($currency),
        items: @js($eligibleItems->map(fn($item) => [
            'id' => $item->id,
            'name' => $item->service_name_snapshot,
            'details' => $item->description_snapshot ?? '',
            'quantity' => (int) $item->quantity,
            'unit_price' => (float) $item->unit_price_snapshot,
            'tax_percentage' => (float) $item->tax_percentage_snapshot,
            'tax_amount' => (float) $item->tax_amount,
            'discount' => (float) $item->discount_snapshot,
            'total' => (float) $item->total,
        ])->values()->toArray()),
        selected: [],
        allChecked: true,

        init() {
            this.selected = this.items.map(i => i.id);
        },

        isSelected(id) {
            return this.selected.includes(id);
        },

        toggleAll() {
            this.selected = this.allChecked ? this.items.map(i => i.id) : [];
        },

        onToggle(e, id) {
            if (e.target.checked) {
                if (!this.selected.includes(id)) this.selected.push(id);
            } else {
                this.selected = this.selected.filter(s => s !== id);
            }
            this.allChecked = this.selected.length === this.items.length;
        },

        get selectedCount() {
            return this.selected.length;
        },

        get totals() {
            const items = this.items.filter(i => this.isSelected(i.id));
            const subtotal = items.reduce((s, i) => s + i.unit_price * i.quantity, 0);
            const discount = items.reduce((s, i) => s + i.discount, 0);
            const tax = items.reduce((s, i) => s + i.tax_amount, 0);
            return {
                subtotal,
                discount,
                tax,
                grand_total: subtotal - discount + tax,
            };
        },
    };
}
</script>
@endpush