@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
$statusColors = [
    'sent' => 'blue',
    'accepted' => 'green',
    'rejected' => 'red',
    'cancelled' => 'gray',
    'draft' => 'amber',
];
$badgeClasses = [
    'blue' => 'bg-blue-50 text-blue-700 ring-blue-200',
    'green' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    'red' => 'bg-red-50 text-red-700 ring-red-200',
    'gray' => 'bg-gray-50 text-gray-600 ring-gray-200',
    'amber' => 'bg-amber-50 text-amber-700 ring-amber-200',
];
$emailColors = [
    'sent' => 'green',
    'failed' => 'red',
    'pending' => 'amber',
];
$chartMonthLabels = $monthly->map(fn ($m) => \Carbon\Carbon::createFromFormat('Y-m', $m->month)->format('M Y'))->values();
@endphp

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">Overview of your quotation activity and email performance.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm font-medium text-gray-500">Total Quotations</span>
                    <h4 class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($stats['total_quotations']) }}</h4>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path d="M14 2v6h6" /><path d="M16 13H8" /><path d="M16 17H8" /><path d="M10 9H8" /></svg>
                </span>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm font-medium text-gray-500">This Month</span>
                    <h4 class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($stats['month_quotations']) }}</h4>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-full bg-violet-50 text-violet-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" /><path d="M16 2v4" /><path d="M8 2v4" /><path d="M3 10h18" /><path d="M9 16l2 2 4-4" /></svg>
                </span>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm font-medium text-gray-500">Today</span>
                    <h4 class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($stats['today_quotations']) }}</h4>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" /><path d="M12 6v6l4 2" /></svg>
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm px-4 py-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2" /><path d="M22 7l-8.97 5.7a1.94 1.94 0 01-2.06 0L2 7" /></svg>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-900">{{ number_format($stats['sent_emails']) }}</p>
                <p class="text-[11px] text-gray-500 truncate">Sent Emails</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 sm:px-6 pt-5 sm:pt-6 pb-4">
            <div>
                <h2 class="font-semibold text-gray-900">Recent Quotations</h2>
                <p class="text-xs text-gray-500 mt-0.5">Latest quotations and their delivery status</p>
            </div>
            <a href="{{ route('admin.quotations.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-700 transition">
                View All
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14" /><path d="M12 5l7 7-7 7" /></svg>
            </a>
        </div>

        @if($recent->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-y border-gray-200 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                        <th class="px-5 sm:px-6 py-3">Quotation</th>
                        <th class="px-5 sm:px-6 py-3">Client</th>
                        <th class="px-5 sm:px-6 py-3">Date</th>
                        <th class="px-5 sm:px-6 py-3 text-right">Amount</th>
                        <th class="px-5 sm:px-6 py-3">Status</th>
                        <th class="px-5 sm:px-6 py-3">Email</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($recent as $quotation)
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="px-5 sm:px-6 py-4">
                            <a href="{{ route('admin.quotations.show', $quotation) }}" class="font-medium text-brand-600 hover:text-brand-700">
                                {{ $quotation->quotation_number }}
                            </a>
                            <p class="text-xs text-gray-500 mt-0.5">{{ Str::limit($quotation->project_description, 40) }}</p>
                        </td>
                        <td class="px-5 sm:px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $quotation->client_name }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $quotation->company_name ?: $quotation->email }}</p>
                        </td>
                        <td class="px-5 sm:px-6 py-4 text-gray-600 whitespace-nowrap">{{ $quotation->quotation_date?->format('M d, Y') }}</td>
                        <td class="px-5 sm:px-6 py-4 text-right font-semibold text-gray-900 whitespace-nowrap">{{ number_format($quotation->grand_total, 2) }}</td>
                        <td class="px-5 sm:px-6 py-4">
                            @php $statusClass = $badgeClasses[$statusColors[$quotation->status] ?? 'gray'] ?? $badgeClasses['gray']; @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ring-1 ring-inset {{ $statusClass }}">
                                {{ ucfirst($quotation->status) }}
                            </span>
                        </td>
                        <td class="px-5 sm:px-6 py-4">
                            @php $emailClass = $badgeClasses[$emailColors[$quotation->email_status] ?? 'gray'] ?? $badgeClasses['gray']; @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium ring-1 ring-inset {{ $emailClass }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2" /><path d="M22 7l-8.97 5.7a1.94 1.94 0 01-2.06 0L2 7" /></svg>
                                {{ ucfirst($quotation->email_status ?? 'Not sent') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 pb-12 flex flex-col items-center justify-center text-center">
            <div class="w-14 h-14 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path d="M14 2v6h6" /></svg>
            </div>
            <p class="text-sm font-medium text-gray-600">No quotations yet</p>
            <p class="text-xs text-gray-400 mt-1">Create your first quotation to see it here.</p>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 gap-6">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-semibold text-gray-900">Quotations by Month</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Monthly volume and value trend</p>
                </div>
                <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Last {{ $monthly->count() }} months</span>
            </div>
            <div class="{{ $monthly->isEmpty() ? 'hidden' : '' }}">
                <canvas id="monthlyChart" class="h-72"></canvas>
            </div>
            @if($monthly->isEmpty())
            <div class="h-72 flex flex-col items-center justify-center text-center">
                <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" /><path d="M16 2v4" /><path d="M8 2v4" /><path d="M3 10h18" /></svg>
                </div>
                <p class="text-sm font-medium text-gray-600">No monthly data yet</p>
                <p class="text-xs text-gray-400 mt-1">Charts will appear once quotations exist.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const monthlyLabels = @json($chartMonthLabels);
const monthlyCounts = @json($monthly->pluck('count')->values());
const monthlyValues = @json($monthly->pluck('value')->values());

function initDashboardCharts() {
    const monthlyEl = document.getElementById('monthlyChart');
    if (monthlyEl && monthlyLabels.length) {
        new Chart(monthlyEl, {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [
                    {
                        label: 'Quotations',
                        data: monthlyCounts,
                        backgroundColor: 'rgba(99, 102, 241, 0.75)',
                        hoverBackgroundColor: 'rgba(99, 102, 241, 0.95)',
                        borderRadius: 6,
                        barThickness: 22,
                        maxBarThickness: 28,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Value',
                        type: 'line',
                        data: monthlyValues,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        pointBackgroundColor: '#f59e0b',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        borderWidth: 2.5,
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y1',
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: { usePointStyle: true, boxWidth: 8, font: { size: 12, weight: 600 } },
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        cornerRadius: 10,
                        titleFont: { weight: 700 },
                        callbacks: {
                            label: function (context) {
                                return context.dataset.label + ': ' + (context.dataset.yAxisID === 'y1'
                                    ? Number(context.raw).toLocaleString(undefined, { minimumFractionDigits: 2 })
                                    : Number(context.raw).toLocaleString());
                            },
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(148, 163, 184, 0.15)' },
                        border: { display: false },
                        ticks: { maxTicksLimit: 6, callback: function (value) { return Number(value).toLocaleString(); } },
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: { display: false },
                        border: { display: false },
                        ticks: { maxTicksLimit: 6, callback: function (value) { return Number(value).toLocaleString(); } },
                    },
                    x: {
                        grid: { display: false },
                        border: { display: false },
                    },
                },
            },
        });
    }

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDashboardCharts);
} else {
    initDashboardCharts();
}
</script>
@endpush