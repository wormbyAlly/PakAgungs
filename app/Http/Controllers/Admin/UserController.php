<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // 🔍 SEARCH
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            });
        }

        $users = $query
            ->orderBy('created_at', 'desc')
            ->paginate(8);

        // ⚠️ PENTING: kalau request JSON → balikin JSON
        if ($request->expectsJson()) {
            return response()->json($users);
        }

        // fallback (kalau akses non-AJAX)
        return view('pages.admin.users.index', compact('users'));
    }

    public function create()
    {
        // pages . admin . users . index
        return view('pages.admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,user',
            'password' => 'required|min:8',
        ]);

        $data['password'] = bcrypt($data['password']);
        $data['status'] = 'active';

        User::create($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User berhasil dibuat");
    }
    public function toggleStatus(User $user)
    {
        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Admin tidak dapat dinonaktifkan.',
            ], 403);
        }

        // toggle logic
        $user->status = $user->status === 'active'
            ? 'inactive'
            : 'active';

        $user->save();

        return response()->json([
            'success' => true,
            'status' => $user->status,
        ]);
    }

    public function resetPassword(User $user)
    {
        $newPassword = Str::random(10);

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return back()->with('info', "Password baru: {$newPassword}");
    }

    public function edit(User $user)
    {
        return view('pages.admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => "required|email|unique:users,email,$user->id",
            'role' => 'required|in:admin,user',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|min:8',
        ]);

        // password hanya diubah jika diisi
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }


    public function deactivate(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Admin tidak bisa dinonaktifkan.');
        }

        $user->update([
            'status' => 'inactive'
        ]);

        return back()->with('success', 'User berhasil dinonaktifkan.');
    }
    public function destroy(User $user)
    {
        // Opsional: cegah hapus diri sendiri
        if (auth()->id() === $user->id) {
            return response()->json([
                'message' => 'Tidak bisa menghapus akun sendiri'
            ], 403);
        }

        // ⛔ Admin tidak boleh dihapus
        if ($user->role === 'admin') {
            return response()->json([
                'message' => 'Akun admin tidak dapat dihapus.',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus'
        ]);
    }
}
