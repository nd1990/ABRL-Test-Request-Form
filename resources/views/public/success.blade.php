<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ABRL Test Request Form</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { brand: { 50:'#eaf2fb', 100:'#d2e4f7', 200:'#a6c9f0', 300:'#6ea9e2', 400:'#1f7bd0', 500:'#01589f', 600:'#01458e', 700:'#013a78', 800:'#02306b', 900:'#052b63' }, indigo: { 50:'#eaf2fb', 100:'#d2e4f7', 200:'#a6c9f0', 300:'#6ea9e2', 400:'#1f7bd0', 500:'#01589f', 600:'#01458e', 700:'#013a78', 800:'#02306b', 900:'#052b63' } },
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
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-800 text-white flex items-center justify-center">
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
                    <p class="font-semibold">A copy of your quotation is being emailed to {{ $quotation->email }}.</p>
                    <p class="mt-1">It usually arrives within a minute. Please also check your spam folder.</p>
                </div>
            </div>
            @endif

            <div class="mt-6 border-t border-gray-100 pt-6">
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a id="request-another-link" href="{{ route('quotation.index') }}"
                        class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-brand-500 hover:bg-brand-600 text-white font-semibold text-sm px-6 py-3 rounded-xl shadow-lg shadow-brand-500/20 transition focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 109-9" /><polyline points="3 4 3 12 11 12" /></svg>
                        Request another quotation
                    </a>
                    <button type="button" id="exit-button"
                        class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-white hover:bg-gray-50 text-gray-600 hover:text-gray-800 font-semibold text-sm px-6 py-3 rounded-xl border border-gray-300 transition focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" /><polyline points="16 17 21 12 16 7" /><line x1="21" y1="12" x2="9" y2="12" /></svg>
                        Exit
                    </button>
                </div>
                <p id="request-another-expired" class="hidden mt-3 text-center text-xs text-gray-400">The “Request another quotation” link has expired. Please close this page or open a fresh form link.</p>
            </div>
        </div>

        <div class="mt-6 text-center text-xs text-gray-400">
            &copy; 2026 ABRL. All rights reserved.
        </div>
    </main>
<style>.step-enter{animation:fadeUp .4s ease both}@keyframes fadeUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}</style>
<script>
(function () {
    const link = document.getElementById('request-another-link');
    const note = document.getElementById('request-another-expired');
    if (!link) return;

    const storageKey = 'abrl-request-another-expiry-{{ $quotation->id }}';
    const fiveMinutes = 5 * 60 * 1000;
    let expiresAt = Date.now() + fiveMinutes;

    try {
        const saved = Number(sessionStorage.getItem(storageKey));
        if (saved && saved > Date.now()) {
            expiresAt = saved;
        } else {
            sessionStorage.setItem(storageKey, String(expiresAt));
        }
    } catch (e) {}

    let timer = null;
    function expire() {
        if (timer) window.clearTimeout(timer);
        link.removeAttribute('href');
        link.setAttribute('aria-disabled', 'true');
        link.classList.add('opacity-40', 'pointer-events-none', 'line-through');
        if (note) note.classList.remove('hidden');
    }
    function schedule() {
        const remaining = expiresAt - Date.now();
        if (remaining <= 0) {
            expire();
            return;
        }
        timer = window.setTimeout(schedule, Math.min(remaining, 1000));
    }
    schedule();

    const exitButton = document.getElementById('exit-button');
    if (exitButton) {
        exitButton.addEventListener('click', function () {
            window.location.href = @json(route('quotation.index'));
        });
    }
})();
</script>
</body>
</html>