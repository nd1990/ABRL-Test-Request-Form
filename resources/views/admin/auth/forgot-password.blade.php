<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password - ABRL Test Request Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { brand: { 500:'#3c50e0', 600:'#3c50e0', 700:'#3056d3' } },
                    fontFamily: { sans: ['Inter','ui-sans-serif','system-ui','-apple-system','Segoe UI','Roboto','sans-serif'] }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="font-sans antialiased bg-[#F1F5F9] min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-lg bg-brand-600 text-white flex items-center justify-center mx-auto shadow-lg shadow-brand-600/30 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Forgot Password</h1>
            <p class="text-gray-500 text-sm mt-1">Enter your email to receive a reset link</p>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 sm:p-8 shadow-sm">
            @if (session('success'))
            <div class="mb-4 bg-emerald-50 border border-emerald-200 rounded-lg p-4 text-sm text-emerald-700 font-medium">
                {{ session('success') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="mb-4 bg-rose-50 border border-rose-200 rounded-lg p-4">
                <p class="text-sm font-semibold text-rose-700 mb-1">Unable to proceed</p>
                @foreach ($errors->all() as $error)
                <p class="text-xs text-rose-600">{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('admin.password.email') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500">
                </div>

                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-brand-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                    Send Reset Link
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                </button>
            </form>

            <div class="mt-5 text-center">
                <a href="{{ route('admin.login') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">
                    &larr; Back to login
                </a>
            </div>
        </div>

        <p class="text-center text-xs text-gray-500 mt-6">&copy; 2026 ABRL. All rights reserved.</p>
    </div>
</body>
</html>
