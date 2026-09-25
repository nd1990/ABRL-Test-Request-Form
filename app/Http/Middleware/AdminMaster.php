<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMaster
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = app('admin');

        if (!$admin || $admin->role !== Admin::ROLE_MASTER) {
            abort(403, 'Access denied. Master admin privileges required.');
        }

        return $next($request);
    }
}
