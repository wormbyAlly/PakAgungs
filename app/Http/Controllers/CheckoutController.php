<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use App\Models\Customer;
use App\Services\Sales\InvoiceNumberService;
use App\Services\Accounting\JournalService;
use App\Services\Accounting\DTO\SaleSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        if (!$request->expectsJson()) {
            abort(406, 'JSON required');
        }

        $cart = session('cart');

        if (!$cart || empty($cart['items'])) {
            return response()->json([
                'message' => 'Cart kosong'
            ], 422);
        }

        return DB::transaction(function () use ($cart) {

            // 1️⃣ Invoice number
            $invoice = app(InvoiceNumberService::class)->generate();

            // 2️⃣ Hitung subtotal
            $subtotal = collect($cart['items'])
                ->sum(fn($i) => $i['price'] * $i['qty']);

            // 3️⃣ Simpan SALE sebagai DRAFT
            $sale = Sale::create([
                'invoice_number' => $invoice,
                'user_id'        => auth()->id(),
                'customer_id'    => null,              // ⬅️ PENTING
                'subtotal'       => $subtotal,
                'discount'       => 0,
                'total'          => $subtotal,
                'paid_amount'    => 0,
                'change_amount'  => 0,
                'status'         => 'pending',         // ⬅️ DRAFT
                'tgl_jual'       => null,               // ⬅️ BELUM SAH
            ]);

            // 4️⃣ Simpan SALE ITEMS
            foreach ($cart['items'] as $item) {
                SaleItem::create([
                    'sale_id'   => $sale->id,
                    'product_id' => $item['product_id'],
                    'qty'       => $item['qty'],
                    'price'     => $item['price'],
                    'subtotal'  => $item['price'] * $item['qty'],
                ]);
            }

            // 5️⃣ Bersihkan cart
            session()->forget('cart');

            // 6️⃣ Return ke frontend (POPUP)
            return response()->json([
                'message'        => 'Checkout berhasil',
                'invoice_number' => $invoice,
                'sale_id'        => $sale->id,
                'status'         => 'draft',
            ]);
        });
    }


    private function reduceStockFIFO(
        int $productId,
        int $qtyNeeded,
        int $saleId,
        float $price
    ): void {
        $stocks = Stock::where('product_id', $productId)
            ->where('qty', '>', 0)
            ->orderBy('expired_at')
            ->lockForUpdate()
            ->get();

        foreach ($stocks as $stock) {
            if ($qtyNeeded <= 0) break;

            $usedQty = min($stock->qty, $qtyNeeded);

            SaleItem::create([
                'sale_id'   => $saleId,
                'product_id' => $productId,
                'qty'       => $usedQty,
                'price'     => $price,
                'subtotal'  => $usedQty * $price,
            ]);

            $stock->decrement('qty', $usedQty);
            $qtyNeeded -= $usedQty;
        }

        if ($qtyNeeded > 0) {
            throw new \RuntimeException(
                "Stok tidak mencukupi untuk produk ID {$productId}"
            );
        }
    }
}
