<?php

namespace App\Http\Controllers;

use App\Mail\AdminResetPasswordMail;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AdminPasswordResetController extends Controller
{
    public function showForgotForm()
    {
        return view('admin.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:191'],
        ]);

        // Throttle: max 3 reset requests per hour per IP
        $key = 'admin-reset:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return back()->withErrors([
                'email' => 'Too many reset requests. Please try again later.',
            ])->withInput();
        }

        $admin = Admin::where('email', $data['email'])->first();

        // Always return success to avoid leaking which emails exist
        if ($admin && $admin->is_active) {
            $token = Str::random(60);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $admin->email],
                ['token' => hash('sha256', $token), 'created_at' => now()]
            );

            Mail::to($admin->email, $admin->name)->send(new AdminResetPasswordMail($admin, $token));
        }

        RateLimiter::hit($key, 3600);

        return redirect()->route('admin.password.forgot')
            ->with('success', 'If that email exists in our records, a password reset link has been sent.');
    }

    public function showResetForm(Request $request)
    {
        $email = $request->query('email');
        $token = $request->query('token');

        if (!$email || !$token) {
            return redirect()->route('admin.login')->withErrors([
                'email' => 'Invalid reset link.',
            ]);
        }

        return view('admin.auth.reset-password', [
            'email' => $email,
            'token' => $token,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:191'],
            'token' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $data['email'])
            ->first();

        $status = 'invalid';
        if ($record && hash_equals(hash('sha256', $data['token']), $record->token)) {
            $expired = now()->diffInMinutes($record->created_at) >= 60;
            $admin = Admin::where('email', $data['email'])->first();

            if (!$expired && $admin) {
                $admin->update(['password' => Hash::make($data['password'])]);
                DB::table('password_reset_tokens')->where('email', $data['email'])->delete();
                $status = 'success';
            } else {
                $status = 'expired';
            }
        }

        if ($status === 'success') {
            return redirect()->route('admin.login')->with('success', 'Your password has been reset successfully. Please sign in.');
        }

        if ($status === 'expired') {
            return back()->withErrors(['token' => 'This reset link has expired. Please request a new one.']);
        }

        return back()->withErrors(['token' => 'This reset link is invalid. Please request a new one.']);
    }
}
