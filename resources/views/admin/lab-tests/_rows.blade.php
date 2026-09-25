@php $canDelete = $currentAdmin?->hasPermission('lab_tests.delete'); @endphp

@forelse($labTests as $test)
<tr class="hover:bg-gray-50/50">
    @if($canDelete)
    <td class="px-2 py-3">
        <input type="checkbox" data-check="{{ $test->id }}" value="{{ $test->id }}"
            class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500 cursor-pointer">
    </td>
    @endif
    <td class="px-2 py-3 text-gray-400 font-mono">{{ $test->s_no ?? '' }}</td>
    <td class="px-2 py-3">
        @if($test->nabl_type === 'NABL')
        <span class="inline-flex items-center text-xs font-semibold bg-brand-50 text-brand-700 px-2.5 py-1 rounded-full">NABL</span>
        @else
        <span class="inline-flex items-center text-xs font-semibold bg-amber-50 text-amber-700 px-2.5 py-1 rounded-full">NON NABL</span>
        @endif
    </td>
    <td class="px-2 py-3 text-gray-600" title="{{ $test->discipline }}"><div class="truncate max-w-[160px]">{{ $test->discipline }}</div></td>
    <td class="px-2 py-3 text-gray-600" title="{{ $test->material }}"><div class="truncate max-w-[180px]">{{ $test->material }}</div></td>
    <td class="px-2 py-3 font-medium text-gray-900" title="{{ $test->parameter }}"><div class="truncate max-w-[200px]">{{ $test->parameter }}</div></td>
    <td class="px-2 py-3 text-gray-600" title="{{ $test->method }}"><div class="truncate max-w-[160px]">{{ $test->method }}</div></td>
    <td class="px-2 py-3 text-gray-600">{{ $test->sample_quantity }}</td>
    <td class="px-2 py-3 text-right text-gray-900 font-medium whitespace-nowrap">{{ number_format((float) $test->charges_per_sample, 2) }}</td>
    <td class="px-2 py-3 text-right text-gray-900 font-semibold whitespace-nowrap">{{ number_format((float) $test->total_amount, 2) }}</td>
    <td class="px-2 py-3 text-center">
        @if($test->is_active)
        <span class="inline-flex items-center text-xs font-semibold bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-full">Active</span>
        @else
        <span class="inline-flex items-center text-xs font-semibold bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full">Inactive</span>
        @endif
    </td>
    @if($currentAdmin?->hasAnyPermission(['lab_tests.edit', 'lab_tests.delete']))
    <td class="px-2 py-3 text-right">
        <div class="flex items-center justify-end gap-2">
            @if($currentAdmin?->hasPermission('lab_tests.edit'))
            <button type="button" data-action="edit" data-payload='{{ json_encode($test->toArray()) }}' title="Edit"
                class="inline-flex items-center justify-center w-8 h-8 text-brand-600 hover:text-brand-800 bg-brand-50 hover:bg-brand-100 rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </button>
            @endif
            @if($currentAdmin?->hasPermission('lab_tests.delete'))
            <button type="button" data-action="delete" data-id="{{ $test->id }}" title="Delete"
                class="inline-flex items-center justify-center w-8 h-8 text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
            </button>
            @endif
        </div>
    </td>
    @endif
</tr>
@empty
<tr>
    @php
        $actionCol = $currentAdmin?->hasAnyPermission(['lab_tests.edit', 'lab_tests.delete']) ? 1 : 0;
    @endphp
    <td colspan="{{ ($canDelete ? 1 : 0) + 10 + $actionCol }}" class="px-6 py-16 text-center">
        <div class="flex flex-col items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 3h6M10 3v4.5l-5.3 9.1A2 2 0 006.4 20h11.2a2 2 0 001.7-3.4L14 8.5V3"/></svg>
            </div>
            <p class="text-sm font-medium text-gray-500">No services found.</p>
            <p class="text-xs text-gray-400">Try adjusting your search or filters.</p>
        </div>
    </td>
</tr>
@endforelse
