<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
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
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4" /><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" /></svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">ABRL Test Request Form</h1>
            <p class="text-gray-500 text-sm mt-1">Sign in to the admin panel</p>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 sm:p-8 shadow-sm">
            <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-5">
                @csrf

                @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 rounded-lg p-4">
                    <p class="text-sm font-semibold text-rose-700 mb-1">Unable to sign in</p>
                    @foreach ($errors->all() as $error)
                    <p class="text-xs text-rose-600">{{ $error }}</p>
                    @endforeach
                </div>
                @endif

                @if (session('success'))
                <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 text-sm text-emerald-700 font-medium">
                    {{ session('success') }}
                </div>
                @endif

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500">
                </div>

                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-brand-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                    Sign In
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6" /></svg>
                </button>

                <div class="text-center">
                    <a href="{{ route('admin.password.forgot') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">
                        Forgot your password?
                    </a>
                </div>
            </form>
        </div>

        <p class="text-center text-xs text-gray-500 mt-6">&copy; 2026 ABRL. All rights reserved.</p>
    </div>
</body>
</html>