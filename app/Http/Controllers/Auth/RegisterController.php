<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    public function show()
    {
        return view('pages.auth.signup');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fname' => ['required', 'string', 'max:100'],
            'lname' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc,dns', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'fname.required' => 'First name wajib diisi.',
            'lname.required' => 'Last name wajib diisi.',
        ]);

        $fullName = trim($request->fname . ' ' . $request->lname);

        $user = User::create([
            'name'     => $fullName,
            'email'    => $data['email'],
            'password' => $data['password'],
        ]);

        event(new Registered($user));

        return redirect()->route('auth.login')
            ->with('status', 'Akun berhasil dibuat. Silakan login.');
    }
}
