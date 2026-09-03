@php
    $current = $labTests->currentPage();
    $last = $labTests->lastPage();
    $from = $labTests->firstItem() ?: 0;
    $to = $labTests->lastItem() ?: 0;
    $total = $labTests->total();

    $window = 1;
    $start = max(1, $current - $window);
    $end = min($last, $current + $window);
@endphp
<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <p class="text-xs text-gray-500 whitespace-nowrap">
        Showing <span class="font-medium text-gray-700">{{ $from }}</span> to <span class="font-medium text-gray-700">{{ $to }}</span> of <span class="font-medium text-gray-700">{{ $total }}</span> results
    </p>

    @if($labTests->hasPages())
    <nav class="flex items-center gap-1.5 flex-wrap" aria-label="Pagination">
        <button type="button" data-page="{{ $current - 1 }}" {{ $current <= 1 ? 'disabled' : '' }}
            class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 transition enabled:hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            <span class="hidden sm:inline">Previous</span>
        </button>

        @if($start > 1)
        <button type="button" data-page="1" class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg border border-gray-200 bg-white px-2 text-xs font-medium text-gray-600 transition hover:bg-gray-50">1</button>
        @if($start > 2)
        <span class="inline-flex h-8 items-center px-1 text-xs text-gray-400">…</span>
        @endif
        @endif

        @for($p = $start; $p <= $end; $p++)
        <button type="button" data-page="{{ $p }}"
            class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg border px-2 text-xs font-medium transition {{ $p === $current ? 'border-brand-600 bg-brand-600 text-white shadow-sm' : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50' }}">{{ $p }}</button>
        @endfor

        @if($end < $last)
        @if($end < $last - 1)
        <span class="inline-flex h-8 items-center px-1 text-xs text-gray-400">…</span>
        @endif
        <button type="button" data-page="{{ $last }}" class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg border border-gray-200 bg-white px-2 text-xs font-medium text-gray-600 transition hover:bg-gray-50">{{ $last }}</button>
        @endif

        <button type="button" data-page="{{ $current + 1 }}" {{ $current >= $last ? 'disabled' : '' }}
            class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 transition enabled:hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40">
            <span class="hidden sm:inline">Next</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
    </nav>
    @endif
</div>
