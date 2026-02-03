<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Stock;
use Carbon\Carbon;

class CartService
{
    public static function get()
    {
        return session()->get('cart', []);
    }

    public static function add($productId, $qty = 1)
    {
        $product = Product::findOrFail($productId);

        // 🔒 hitung stock yang BELUM expired
        $availableStock = Stock::where('product_id', $productId)
            ->where('expired', '>', Carbon::today())
            ->sum('qty');

        $cart = self::get();
        $currentQty = $cart[$productId]['qty'] ?? 0;

        if ($currentQty + $qty > $availableStock) {
            throw new \Exception('Stock tidak mencukupi');
        }

        $cart[$productId] = [
            'product_id' => $productId,
            'name'       => $product->name,
            'price'      => $product->price,
            'qty'        => $currentQty + $qty,
            'subtotal'  => ($currentQty + $qty) * $product->price,
        ];

        session()->put('cart', $cart);

        return $cart;
    }

    public static function update($productId, $qty)
    {
        $cart = self::get();

        if (!isset($cart[$productId])) return;

        if ($qty <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId]['qty'] = $qty;
            $cart[$productId]['subtotal'] =
                $qty * $cart[$productId]['price'];
        }

        session()->put('cart', $cart);
    }

    public static function remove($productId)
    {
        $cart = self::get();
        unset($cart[$productId]);
        session()->put('cart', $cart);
    }

    public static function clear()
    {
        session()->forget('cart');
    }

    public static function total()
    {
        return collect(self::get())->sum('subtotal');
    }
}
