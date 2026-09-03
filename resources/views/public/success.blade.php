<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ABRL Test Request Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { brand: { 500:'#6366f1', 600:'#4f46e5', 700:'#4338ca' } },
                    fontFamily: { sans: ['Inter','ui-sans-serif','system-ui','-apple-system','Segoe UI','Roboto','sans-serif'] }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="font-sans antialiased bg-slate-50 min-h-screen flex flex-col">
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-3xl mx-auto px-4 py-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4" /><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" /></svg>
            </div>
            <h1 class="font-bold text-gray-900">ABRL Test Request Form</h1>
        </div>
    </header>

    <main class="flex-1 max-w-3xl mx-auto px-4 py-10 w-full">
        <div class="text-center step-enter">
            <div class="mx-auto w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center mb-5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 11-5.93-9.14" /><path d="M22 4L12 14.01l-3-3" /></svg>
            </div>
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Your Quotation is Ready!</h2>
            <p class="text-gray-500 mt-2">Thank you <span class="font-semibold text-gray-700">{{ $quotation->client_name }}</span>.</p>
        </div>

        <div class="mt-8 bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 border-b border-gray-100 pb-5 mb-5">
                <div>
                    <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Quotation Number</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">{{ $quotation->quotation_number }}</p>
                </div>
                <div class="text-left sm:text-right">
                    <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Tests Included</p>
                    <p class="text-xl font-bold text-indigo-600 mt-1">{{ $quotation->items->count() }} test(s)</p>
                </div>
            </div>

            <div class="mb-5">
                <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Selected Tests</p>
                <ul class="divide-y divide-gray-100">
                    @forelse($quotation->items as $item)
                    <li class="py-2.5 flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-800">{{ $item->service_name_snapshot }}</p>
                            @if($item->description_snapshot)
                            <p class="text-xs text-gray-400 mt-0.5 whitespace-pre-line">{{ $item->description_snapshot }}</p>
                            @endif
                        </div>
                        <span class="text-xs text-gray-500 shrink-0 mt-0.5">{{ $item->quantity }} sample(s)</span>
                    </li>
                    @empty
                    <li class="text-sm text-gray-400">No tests were included.</li>
                    @endforelse
                </ul>
            </div>

            @if($quotation->email_status === 'sent')
            <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-sm text-emerald-700 mb-5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" /><polyline points="22,6 12,13 2,6" /></svg>
                <div>
                    <p class="font-semibold">A copy of your quotation has been emailed to {{ $quotation->email }}</p>
                </div>
            </div>
            @elseif($quotation->email_status === 'failed')
            <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-700 mb-5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" /><line x1="12" y1="9" x2="12" y2="13" /><line x1="12" y1="17" x2="12.01" y2="17" /></svg>
                <div>
                    <p class="font-semibold">We could not email your quotation right now.</p>
                    <p class="mt-1">Please try again a little later.</p>
                </div>
            </div>
            @else
            <div class="flex items-start gap-3 bg-slate-50 border border-gray-100 rounded-xl p-4 text-sm text-gray-600 mb-5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" /><polyline points="12 6 12 12 16 14" /></svg>
                <div>
                    <p>A copy of your quotation will be emailed to {{ $quotation->email }}.</p>
                </div>
            </div>
            @endif

            <div class="mt-6 text-center">
                <a href="{{ route('quotation.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 transition font-medium">Request another quotation</a>
            </div>
        </div>

        <div class="mt-6 text-center text-xs text-gray-400">
            &copy; 2026 ABRL. All rights reserved.
        </div>
    </main>
<style>.step-enter{animation:fadeUp .4s ease both}@keyframes fadeUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}</style>
</body>
</html>