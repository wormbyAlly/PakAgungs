<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthRequired
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {

            return redirect()
                ->route('pages.auth.login')
                ->with('auth_error', 'Anda harus login terlebih dahulu.');
        }

        return $next($request);
    }
}
