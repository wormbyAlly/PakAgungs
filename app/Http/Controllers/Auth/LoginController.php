<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function show()
    {
        return view('pages.auth.signin');
    }

    public function login(Request $request)
    {
        // ---- RATE LIMITER ----
        $this->ensureIsNotRateLimited($request);

        // ---- VALIDASI INPUT ----
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('email', 'password');

        // ---- AUTENTIKASI ----
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey($request));

            // Pesan error tidak boleh spesifik (security)
            throw ValidationException::withMessages([
                'email' => "Email atau password salah.",
            ]);
        }

        // Jika sukses login
        $request->session()->regenerate();
        RateLimiter::clear($this->throttleKey($request));

        $user = Auth::user();
        $user->last_login_at = now();
        $user->save();

        return redirect()->intended(
            Auth::user()->role === 'admin'
                ? '/admin/dashboard'
                : '/dashboard'
        );
    }

    // ---- LOGOUT ----
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('pages.auth.login')
            ->with('info', 'Anda telah logout.');
    }

    // ---- RATE LIMIT FUNCTIONS ----
    public function ensureIsNotRateLimited(Request $request)
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        throw ValidationException::withMessages([
            'email' => 'Terlalu banyak percobaan login. Coba lagi nanti.',
        ]);
    }

    public function throttleKey(Request $request)
    {
        return Str::lower($request->input('email')) . '|' . $request->ip();
    }
}
