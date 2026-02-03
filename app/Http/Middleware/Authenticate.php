<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    public function handle($request, Closure $next)
    {
        if (! Auth::check()) {
            session()->flash('auth_required', 'Anda harus login terlebih dahulu.');
            return redirect()->route('pages.auth.login');
        }

        return $next($request);
    }

}
