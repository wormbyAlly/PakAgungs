<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        // Tidak login → login
        if (!$user) {
            return redirect()
                ->route('pages.auth.login')
                ->with('auth_required', 'Anda harus login terlebih dahulu.');
        }

        // User tidak aktif
        if ($user->status !== 'active') {
            auth()->logout();

            return redirect()
                ->route('pages.auth.login')
                ->with('auth_error', 'Akun Anda sudah tidak aktif.');
        }

        // Role tidak sesuai → STOP
        if (!in_array($user->role, $roles)) {
            abort(403);
        }

        return $next($request);
    }
}
