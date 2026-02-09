<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * Display a listing of the teachers.
     */
    public function index(Request $request)
    {
        $query = Teacher::query();

        // 🔍 SEARCH (hanya nama)
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where('name', 'like', "%{$search}%");
        }

        $teachers = $query->latest()->paginate(8);

        // 🔁 Untuk Alpine / AJAX
        if ($request->expectsJson()) {
            return response()->json($teachers);
        }

        return view('pages.admin.teachers.index', compact('teachers'));
    }

    /**
     * Show the detail of teacher.
     */
    public function show(Teacher $teacher)
    {
        return view('pages.admin.teachers.show', compact('teacher'));
    }

    /**
     * Show the form for creating a new teacher.
     */
    public function create()
    {
        return view('pages.admin.teachers.create');
    }

    /**
     * Store a newly created teacher.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $teacher = Teacher::create($data);

        return response()->json([
            'message' => 'Guru berhasil ditambahkan',
            'data'    => $teacher
        ], 201);
    }

    /**
     * Show the form for editing the specified teacher.
     */
    public function edit(Teacher $teacher)
    {
        return view('pages.admin.teachers.edit', compact('teacher'));
    }

    /**
     * Update the specified teacher.
     */
    public function update(Request $request, Teacher $teacher)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $teacher->update($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Guru berhasil diperbarui.');
    }

    /**
     * Remove the specified teacher.
     */
    public function destroy(Teacher $teacher)
    {
        $teacher->delete();

        return response()->json([
            'message' => 'Guru berhasil dihapus'
        ]);
    }

    /**
     * Search teacher (untuk autocomplete / modal)
     */
    public function search(Request $request)
    {
        $search = $request->search;

        $teachers = Teacher::where('name', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'name']);

        return response()->json($teachers);
    }
}
