<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        
        return view('pages.admin.carts.index');
    }

    public function current()
    {
        return response()->json($this->cartResponse());
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'qty'        => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($request->product_id);

        $availableStock = Stock::where('product_id', $product->id)
            ->where('qty', '>', 0)
            ->sum('qty');

        if ($availableStock <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk tidak memiliki stok',
                'max_qty' => 0,
            ], 422);
        }

        $cart = session()->get('cart', ['items' => []]);
        $currentQty = $cart['items'][$product->id]['qty'] ?? 0;
        $requestedQty = $currentQty + $request->qty;

        if ($requestedQty > $availableStock) {
            return response()->json([
                'status' => 'error',
                'message' => "Stok tersedia hanya {$availableStock}",
                'max_qty' => $availableStock,
            ], 422);
        }

        $cart['items'][$product->id] = [
            'product_id' => $product->id,
            'name'       => $product->name,
            'price'      => $product->price,
            'qty'        => $requestedQty,
        ];

        session()->put('cart', $cart);

        return response()->json([
            'status' => 'success',
            ...$this->cartResponse()
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => ['required'],
            'qty'        => ['required', 'integer', 'min:1'],
        ]);

        $cart = session()->get('cart');

        if (!$cart || !isset($cart['items'][$request->product_id])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item tidak ditemukan'
            ], 404);
        }

        $availableStock = Stock::where('product_id', $request->product_id)
            ->where('qty', '>', 0)
            ->sum('qty');

        if ($request->qty > $availableStock) {
            return response()->json([
                'status' => 'error',
                'message' => "Qty melebihi stok ({$availableStock})",
                'max_qty' => $availableStock,
            ], 422);
        }

        $cart['items'][$request->product_id]['qty'] = $request->qty;

        session()->put('cart', $cart);

        return response()->json([
            'status' => 'success',
            ...$this->cartResponse()
        ]);
    }



    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => ['required'],
        ]);

        $cart = session()->get('cart');

        if ($cart && isset($cart['items'][$request->product_id])) {
            unset($cart['items'][$request->product_id]);

            if (empty($cart['items'])) {
                session()->forget('cart');
            } else {
                session()->put('cart', $cart);
            }
        }

        return response()->json($this->cartResponse());
    }

    public function clear()
    {
        session()->forget('cart');

        return response()->json([
            'items'    => [],
            'subtotal' => 0
        ]);
    }

    /**
     * =========================
     * INTERNAL RESPONSE BUILDER
     * =========================
     */
    private function cartResponse(): array
    {
        $cart = session()->get('cart', ['items' => []]);

        $items = array_values($cart['items']);

        $subtotal = collect($items)
            ->sum(fn($i) => $i['price'] * $i['qty']);

        return [
            'items'    => $items,
            'subtotal' => $subtotal
        ];
    }
}
