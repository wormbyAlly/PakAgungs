<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Journal;
use App\Models\Coa;
use App\Services\Sales\InvoiceNumberService;
use App\Models\InvoiceService;



class SaleController extends Controller
{
    DB::transaction(function () use ($request) {

    // 1️⃣ Generate invoice
    $invoiceNo = InvoiceService::generate();

    // 2️⃣ Buat SALE (header)
    $sale = Sale::create([
        'invoice_number' => $invoiceNo,
        'customer_id'    => $request->customer_id,
        'user_id'        => auth()->id(),
        'tgl_jual'       => $request->tgl_jual,
        'total_harga'    => $grandTotal,
        'diskon'         => $discount,
    ]);

    // 3️⃣ Buat SALE DETAILS dari CART
    foreach (session('cart') as $item) {
        SaleDetail::create([
            'sale_id'   => $sale->id,
            'product_id'=> $item['product_id'],
            'harga'     => $item['price'],
            'qty'       => $item['qty'],
        ]);

        // 4️⃣ FIFO stock deduction
        app(StockService::class)->deductFIFO(
            productId: $item['product_id'],
            qty: $item['qty'],
            saleId: $sale->id
        );
    }

    // 5️⃣ JURNAL (DI SINI)
    $summary = new SaleSummary(
        subtotal: $subtotal,
        discount: $discount,
        grandTotal: $grandTotal
    );

    app(JournalService::class)->recordSale($sale, $summary);

    // 6️⃣ Clear cart
    session()->forget('cart');
});

}
