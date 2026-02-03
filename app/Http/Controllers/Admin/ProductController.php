<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use SoftDeletes;
use Carbon\Carbon;
use App\Models\stock;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $query = Product::withSum(
            ['stocks as total_qty' => function ($q) {
                $q->where('expired', '>', Carbon::today());
            }],
            'qty'
        );


        if ($request->filled('search')) {
            $search = trim($request->search);


            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('price', 'like', "%{$search}%");
                if (is_numeric($search)) {
                    $q->orWhere('price', $search);
                }
            });
        }

        $products = $query->latest()->paginate(8);

        if ($request->expectsJson()) {
            return response()->json(
                $products->through(function ($product) {
                    $product->available_stock = (int) ($product->total_qty ?? 0);
                    return $product;
                    $totalStock = Stock::where('product_id', $product->id)
                        ->whereDate('expired', '>', now())
                        ->sum('qty');
                })
            );
        }


        return view('pages.admin.products.index', compact('products'));
    }


    /**
     * Show detail product.
     */
    public function show(Product $product)
    {
        $today = now();

        // 🔹 Query dasar stock produk
        $stocksQuery = $product->stocks()->orderBy('expired');

        // 🔹 Pagination untuk tabel
        $stocks = $stocksQuery->paginate(10);

        // 🔹 TOTAL STOCK
        $totalStock = $product->stocks()->sum('qty');

        // 🔹 EXPIRED
        $expiredStock = $product->stocks()
            ->where('expired', '<=', $today)
            ->sum('qty');

        // 🔹 AVAILABLE
        $availableStock = $product->stocks()
            ->where('expired', '>', $today)
            ->sum('qty');

        // 🔹 NEAR EXPIRED (30 hari)
        $nearExpiredStock = $product->stocks()
            ->whereBetween('expired', [
                $today,
                $today->copy()->addDays(30)
            ])
            ->sum('qty');

        // 🔹 TANGGAL EXPIRED PALING DEKAT (BELUM EXPIRED)
        $nearestExpiredDate = $product->stocks()
            ->where('expired', '>', $today)
            ->orderBy('expired')
            ->value('expired'); // <- ambil 1 tanggal saja

        return view('pages.admin.products.show', compact(
            'product',
            'stocks',
            'totalStock',
            'availableStock',
            'expiredStock',
            'nearExpiredStock',
            'nearestExpiredDate'
        ));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        return view('pages.admin.products.create');
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code'  => 'required|string|max:50',
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        if (Product::where('code', $request->code)->exists()) {
            return response()->json([
                'message' => 'Kode produk sudah digunakan'
            ], 422);
        }

        $product = Product::create($request->only('code', 'name', 'price'));

        return response()->json([
            'message' => 'Produk berhasil ditambahkan',
            'data'    => $product
        ], 201);
    }


    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        return view('pages.admin.products.edit', compact('product'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'code'  => ['required', 'string', 'max:50', 'unique:products,code,' . $product->id],
            'name'  => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $product->update($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product berhasil diperbarui.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->stocks()->exists()) {
            return response()->json([
                'message' => 'Product masih memiliki stok'
            ], 422);
        }

        $product->delete();

        return response()->json([
            'message' => 'User berhasil dihapus'
        ]);
    }
    public function search(Request $request)
    {
        $search = $request->search;
        $today = now()->toDateString();

        $products = Product::where('name', 'like', "%{$search}%")
            ->orWhere('id', $search)
            ->limit(10)
            ->get()
            ->map(function ($product) use ($today) {
                $availableStock = $product->stocks()
                    ->where('expired', '>', $today)
                    ->sum('qty');

                return [
                    'id'              => $product->id,
                    'name'            => $product->name,
                    'price'           => $product->price,
                    'available_stock' => $availableStock,
                ];
            });

        return response()->json($products);
    }
}
