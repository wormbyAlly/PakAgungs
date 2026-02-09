<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $query = Produk::query();

        // 🔍 SEARCH (hanya nama)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('name', 'like', "%{$search}%");
        }

        $produks = $query->latest()->paginate(8);

        // 🔁 Untuk Alpine / AJAX
        if ($request->expectsJson()) {
            return response()->json($produks);
        }

        return view('pages.admin.produks.index', compact('produks'));
    }

    /**
     * Show the detail of product.
     */
    public function show(Produk $produk)
    {
        return view('pages.admin.produks.show', compact('produk'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        return view('pages.admin.produks.create');
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $produk = Produk::create($data);

        return response()->json([
            'message' => 'Produk berhasil ditambahkan',
            'data'    => $produk
        ], 201);
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Produk $produk)
    {
        return view('pages.admin.produks.edit', compact('produk'));
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Produk $produk)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $produk->update($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('admin.produks.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Produk $produk)
    {
        $produk->delete();

        return response()->json([
            'message' => 'Produk berhasil dihapus'
        ]);
    }

    /**
     * Search product (untuk autocomplete / modal)
     */
    public function search(Request $request)
    {
        $search = $request->search;

        $produks = Produk::where('name', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'name']);

        return response()->json($produks);
    }
}
