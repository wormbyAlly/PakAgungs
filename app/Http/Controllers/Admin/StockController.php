<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StockController extends Controller
{
    /**
     * Display a listing of the stocks.
     */
    public function index(Request $request)
    {
        $products = Product::orderBy('name')->get();

        $query = Stock::with(['product', 'user'])
            ->orderBy('expired', 'asc');

        // 🔍 SEARCH (optional, siap kalau nanti mau dipakai)
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
                ->orWhere('lot_number', 'like', "%{$search}%");
        }

        $stocks = $query->paginate(10);

        // ✅ MODE AJAX
        if ($request->expectsJson()) {
            return response()->json([
                'data' => $stocks->getCollection()->transform(function ($stock) {
                    return [
                        'id' => $stock->id,

                        // PRODUCT
                        'product' => [
                            'id'   => $stock->product->id,
                            'name' => $stock->product->name,
                        ],

                        // STOCK DATA
                        'lot_number' => $stock->lot_number,
                        'qty'        => $stock->qty,

                        // EXPIRED
                        'expired' => $stock->expired->toDateString(),
                        'expired_formatted' => $stock->expired->format('d M Y'),
                        'is_expired' => $stock->expired->isPast(),

                        // USER
                        'user_id'   => $stock->user_id,
                        'user_name' => $stock->user->name ?? '-',

                        // PERMISSION (frontend tinggal pakai)
                        'can_manage' =>
                        auth()->user()->role === 'admin'
                            || $stock->user_id === auth()->id(),
                    ];
                }),

                // PAGINATION
                'current_page' => $stocks->currentPage(),
                'last_page'    => $stocks->lastPage(),
                'total'        => $stocks->total(),
            ]);
        }

        // 🖥️ MODE HTML
        return view('pages.admin.stocks.index', compact('products'));
    }



    /**
     * Store a newly created stock in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'lot_number' => ['required', 'string', 'max:100'],
            'expired'    => ['required', 'date', 'after:today'],
            'qty'        => ['required', 'integer', 'min:1'],
        ]);

        // Cegah LOT ganda untuk product yang sama
        $exists = Stock::where('product_id', $validated['product_id'])
            ->where('lot_number', $validated['lot_number'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'LOT sudah ada'], 422);
        }


        Stock::create([
            'product_id' => $validated['product_id'],
            'lot_number' => $validated['lot_number'],
            'expired'    => $validated['expired'],
            'qty'        => $validated['qty'],
            'user_id'    => auth()->id(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Show the form for editing the specified stock.
     */
    public function edit(Stock $stock)
    {
        $products = Product::orderBy('name')->get();

        return view('pages.admin.stocks.edit', compact('stock', 'products'));
    }

    /**
     * Update the specified stock in storage.
     */
    public function update(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'lot_number' => ['required', 'string', 'max:100'],
            'expired'    => ['required', 'date'],
            'qty'        => ['required', 'integer', 'min:0'],
        ]);

        // Cegah LOT ganda (kecuali dirinya sendiri)
        $exists = Stock::where('product_id', $validated['product_id'])
            ->where('lot_number', $validated['lot_number'])
            ->where('id', '!=', $stock->id)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['lot_number' => 'LOT number sudah ada untuk produk ini.'])
                ->withInput();
        }

        $stock->update($validated);

        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified stock from storage.
     */
    public function destroy(Stock $stock)
    {
        // Catatan RS:
        // Idealnya stock TIDAK dihapus jika sudah pernah dipakai transaksi
        // Untuk sekarang (Day 7), kita izinkan delete manual

        $stock->delete();

        return response()->json(['success' => true]);
    }
}
