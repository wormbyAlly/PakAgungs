<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\Loan;
use Illuminate\Support\Facades\Storage;


class ItemController extends Controller
{
    /**
     * Display a listing of the items.
     */
    public function show(Item $item)
    {
        $item->load('category');

        $user = auth()->user();

        // cek apakah user sedang meminjam item ini
        $activeLoan = Loan::where('item_id', $item->id)
            ->where('user_id', $user->id)
            ->where('status', 'borrowed')
            ->first();

        return view('pages.admin.items.show', compact(
            'item',
            'activeLoan'
        ));
    }

    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();

        $query = Item::with('category')
            ->orderBy('name');

        // 🔍 SEARCH (nama item / kategori)
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where('name', 'like', "%{$search}%")
                ->orWhereHas('category', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        }

        $items = $query->paginate(10);

        // ✅ MODE AJAX
        if ($request->expectsJson()) {
            return response()->json([
                'data' => $items->getCollection()->transform(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'stock' => $item->stock,
                        'is_active' => $item->is_active,

                        'image' => $item->image
                            ? asset('storage/' . $item->image)
                            : null,


                        'category' => [
                            'id'   => $item->category->id,
                            'name' => $item->category->name,
                        ],
                    ];
                }),

                // PAGINATION
                'current_page' => $items->currentPage(),
                'last_page'    => $items->lastPage(),
                'total'        => $items->total(),
            ]);
        }

        // 🖥️ MODE HTML
        return view('pages.admin.items.index', compact('categories'));
    }

    /**
     * Store a newly created item.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'stock'       => ['required', 'integer', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'is_active'   => ['nullable', 'boolean'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                ->store('items', 'public');
        }

        Item::create($validated);

        return response()->json(['success' => true]);
    }


    /**
     * Show the form for editing the specified item.
     */
    public function edit(Item $item)
    {
        $categories = Category::orderBy('name')->get();

        return view('pages.admin.items.edit', compact('item', 'categories'));
    }

    /**
     * Update the specified item.
     */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'stock'       => ['required', 'integer', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'is_active'   => ['nullable', 'boolean'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {

            // hapus gambar lama kalau ada
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }

            $validated['image'] = $request->file('image')
                ->store('items', 'public');
        }

        $item->update($validated);

        return response()->json(['success' => true]);
    }


    /**
     * Remove the specified item.
     */
    public function destroy(Item $item)
    {
        // Catatan:
        // Idealnya item tidak dihapus jika sudah dipakai transaksi peminjaman
        // Untuk tahap awal kita izinkan delete

        $item->delete();

        return response()->json(['success' => true]);
    }
}
