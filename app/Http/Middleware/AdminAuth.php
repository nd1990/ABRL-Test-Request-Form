<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Please login to continue.');
        }

        $admin = \App\Models\Admin::find(session('admin_id'));
        if (!$admin || !$admin->is_active) {
            session()->forget('admin_id');
            return redirect()->route('admin.login')->with('error', 'Your account is inactive or not found.');
        }

        app()->instance('admin', $admin);
        view()->share('currentAdmin', $admin);

        return $next($request);
    }
}
