<?php

namespace App\Http\Controllers\Penjualan;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Customer;
use App\Services\Accounting\JournalService;
use App\Services\Accounting\DTO\SaleSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleRegistrationController extends Controller
{
    /**
     * Halaman Pendaftaran Penjualan
     */
    public function index()
    {
        return view('pages.penjualan.pendaftaran');
    }

    /**
     * Cari penjualan (DRAFT saja)
     * by invoice / nama / telp / email
     */
    public function search(Request $request)
    {
        if (! $request->expectsJson()) {
            return response()->json([
                'message' => 'Invalid request'
            ], 406);
        }

        $keyword = $request->input('keyword');

        if (! $keyword) {
            return response()->json([
                'message' => 'Keyword wajib diisi'
            ], 422);
        }

        $sale = Sale::with(['items.product'])
            ->where('status', 'pending')
            ->where(function ($q) use ($keyword) {
                $q->where('invoice_number', $keyword)
                    ->orWhereHas('customer', function ($c) use ($keyword) {
                        $c->where('name', 'like', "%{$keyword}%")
                            ->orWhere('phone', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    });
            })
            ->first();

        if (! $sale) {
            return response()->json([
                'message' => 'Transaksi draft tidak ditemukan'
            ], 404);
        }

        /**
         * RESPONSE HARUS EKSPLISIT
         * jangan lempar model mentah
         */
        return response()->json([
            'id' => $sale->id,
            'invoice_number' => $sale->invoice_number,
            'customer' => $sale->customer ? [
                'name'  => $sale->customer->name,
                'phone' => $sale->customer->phone,
                'email' => $sale->customer->email,
            ] : [
                'name' => '',
                'phone' => '',
                'email' => '',
            ],
            'items' => $sale->items->map(fn($item) => [
                'id'       => $item->id,
                'qty'      => $item->qty,
                'price'    => $item->price,
                'subtotal' => $item->subtotal,
                'product'  => [
                    'name' => $item->product->name,
                ],
            ]),
        ]);
    }

    /**
     * Finalisasi penjualan (DRAFT → FINAL)
     */
    public function finalize(Request $request, Sale $sale)
    {

        logger()->info('FINALIZE REQUEST', $request->all());

        if ($sale->status !== 'pending') {
            return response()->json([
                'message' => 'Penjualan sudah difinalisasi'
            ], 422);
        }

        $request->validate([
            'name'   => ['required', 'string'],
            'phone'  => ['nullable', 'string'],
            'email'  => ['nullable', 'email'],
            'discount' => ['nullable', 'numeric', 'min:0'],
        ]);

        return DB::transaction(function () use ($request, $sale) {

            /**
             * 1️⃣ Cari / Buat Customer
             */
            $customer = Customer::query()
                ->where('email', $request->email)
                ->orWhere('phone', $request->phone)
                ->first();

            if ($request->filled('name')) {
                $customer = Customer::firstOrCreate(
                    ['email' => $request->email],
                    [
                        'name' => $request->name,
                        'phone' => $request->phone,
                    ]
                );

                $sale->customer_id = $customer->id;
            }


            /**
             * 2️⃣ Hitung ulang total FINAL
             */
            $subtotal = $sale->items->sum('subtotal');
            $discount = $request->discount ?? 0;
            $grandTotal = max($subtotal - $discount, 0);

            /**
             * 3️⃣ Update Sale
             */
            $sale->update([
                'customer_id' => $customer->id,
                'discount'    => $discount,
                'total'       => $grandTotal,
                'status'      => 'paid', // FINAL
                'tgl_jual'    => now()->toDateString(),
            ]);

            /**
             * 4️⃣ Buat Journal (BARU DI SINI)
             */
            app(JournalService::class)->recordSale(
                $sale,
                new SaleSummary(
                    subtotal: $subtotal,
                    discount: $discount,
                    grandTotal: $grandTotal
                )
            );

            return response()->json([
                'message' => 'Penjualan berhasil difinalisasi',
                'invoice' => $sale->invoice_number,
            ]);
        });
    }
}
