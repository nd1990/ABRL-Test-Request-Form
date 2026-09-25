@forelse($quotations as $q)
<tr class="hover:bg-gray-50/50">
    <td class="px-4 py-3 font-medium text-brand-600 whitespace-nowrap">
        <a href="{{ route('admin.quotations.show', $q) }}" class="hover:underline">{{ $q->quotation_number }}</a>
    </td>
    <td class="px-4 py-3 text-gray-800 whitespace-nowrap">{{ $q->client_name }}</td>
    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $q->company_name }}</td>
    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $q->email }}</td>
    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $q->quotation_date->format('d M Y') }}</td>
    <td class="px-4 py-3 text-center whitespace-nowrap">
        @if($q->status === 'accepted')
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Accepted</span>
        @elseif($q->status === 'rejected')
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">Rejected</span>
        @elseif($q->status === 'cancelled')
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-700">Cancelled</span>
        @elseif($q->status === 'draft')
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Draft</span>
        @else
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Sent</span>
        @endif
    </td>
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
            @if($currentAdmin?->hasPermission('quotations.edit'))
            <a href="{{ route('admin.quotations.edit', $q) }}"
               class="p-1.5 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </a>
            @endif
            <a href="{{ route('admin.quotations.pdf', $q) }}"
               class="p-1.5 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Download PDF">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            </a>
            @if($currentAdmin?->hasPermission('quotations.delete'))
            <form method="POST" action="{{ route('admin.quotations.destroy', $q) }}" class="inline"
                  x-data onsubmit="return confirm('Are you sure you want to permanently delete this quotation?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="p-1.5 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Delete">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                </button>
            </form>
            @endif
            @if($currentAdmin?->hasPermission('quotations.send_email'))
            <form method="POST" action="{{ route('admin.quotations.resend', $q) }}" class="inline" x-data>
                @csrf
                <button type="submit"
                        class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Resend Email">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 014-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 01-4 4H3"/></svg>
                </button>
            </form>
            @endif
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="9" class="px-6 py-16 text-center">
        <div class="flex flex-col items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
            <p class="text-sm font-medium text-gray-500">No quotations found.</p>
            <p class="text-xs text-gray-400">Try adjusting your search or filters.</p>
        </div>
    </td>
</tr>
@endforelse
