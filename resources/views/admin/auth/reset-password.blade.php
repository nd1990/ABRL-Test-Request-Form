<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - ABRL Test Request Form</title>
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
            <h1 class="text-2xl font-bold text-gray-900">Choose a New Password</h1>
            <p class="text-gray-500 text-sm mt-1">Set a new password for your account</p>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 sm:p-8 shadow-sm">
            @if ($errors->any())
            <div class="mb-4 bg-rose-50 border border-rose-200 rounded-lg p-4">
                <p class="text-sm font-semibold text-rose-700 mb-1">Unable to reset password</p>
                @foreach ($errors->all() as $error)
                <p class="text-xs text-rose-600">{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('admin.password.reset.submit') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 text-xs text-gray-600">
                    Resetting password for <strong>{{ $email }}</strong>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">New Password</label>
                    <input id="password" type="password" name="password" required autofocus minlength="8" placeholder="Minimum 8 characters"
                        class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirm New Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500">
                </div>

                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-brand-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                    Reset Password
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-500 mt-6">&copy; 2026 ABRL. All rights reserved.</p>
    </div>
</body>
</html>
