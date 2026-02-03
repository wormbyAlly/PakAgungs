<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductSearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $search = $request->query('search');

        return Product::query()
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('id', $search)
                    ->orWhere('price', $search);
            })
            ->withSum(['stocks as available_stock' => function ($q) {
                $q->where('qty', '>', 0);
            }], 'qty')
            ->limit(10)
            ->get()
            ->map(fn($p) => [
                'id'              => $p->id,
                'name'            => $p->name,
                'price'           => $p->price,
                'available_stock' => (int) $p->available_stock,
            ]);
    }
}
