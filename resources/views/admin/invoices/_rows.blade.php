@forelse($invoices as $i)
<tr class="hover:bg-gray-50/50">
    <td class="px-4 py-3 font-medium text-brand-600 whitespace-nowrap">
        <a href="{{ route('admin.invoices.show', $i) }}" class="hover:underline">{{ $i->invoice_number }}</a>
    </td>
    <td class="px-4 py-3 text-gray-800 whitespace-nowrap">{{ $i->client_name }}</td>
    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $i->company_name }}</td>
    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
        @if($i->quotation)
            <a href="{{ route('admin.quotations.show', $i->quotation) }}" class="text-gray-600 hover:text-brand-600 hover:underline">{{ $i->quotation->quotation_number }}</a>
        @else
            —
        @endif
    </td>
    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $i->invoice_date->format('d M Y') }}</td>
    <td class="px-4 py-3 text-gray-800 text-right font-medium whitespace-nowrap">{{ number_format((float) $i->grand_total, 2) }}</td>
    <td class="px-4 py-3 text-center whitespace-nowrap">
        @if($i->status === 'issued')
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Issued</span>
        @elseif($i->status === 'paid')
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Paid</span>
        @else
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 capitalize">{{ $i->status }}</span>
        @endif
    </td>
    <td class="px-4 py-3 text-right whitespace-nowrap sticky right-0 bg-white border-l border-gray-200">
        <div class="flex items-center justify-end gap-1">
            <a href="{{ route('admin.invoices.show', $i) }}"
               class="p-1.5 text-gray-500 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition" title="View">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </a>
            <a href="{{ route('admin.invoices.pdf', $i) }}"
               class="p-1.5 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Download PDF">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            </a>
            @if($currentAdmin?->hasPermission('invoices.delete'))
            <form method="POST" action="{{ route('admin.invoices.destroy', $i) }}" class="inline"
                  x-data onsubmit="return confirm('Delete this invoice? Its parameters will be available for re-invoicing.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="p-1.5 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Delete">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                </button>
            </form>
            @endif
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="px-6 py-16 text-center">
        <div class="flex flex-col items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
            <p class="text-sm font-medium text-gray-500">No invoices found.</p>
            <p class="text-xs text-gray-400">Generate invoices from accepted quotations.</p>
        </div>
    </td>
</tr>
@endforelse