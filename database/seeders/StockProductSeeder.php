<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use Carbon\Carbon;

class StockProductSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * CONFIG YANG BISA KAMU ATUR
         */
        $productId = 1;     // ← ganti product ID
        $totalStock = 15;   // ← berapa stock mau dibuat
        $userId = 1;        // ← pastikan user ini ADA

        $product = Product::find($productId);

        if (! $product) {
            $this->command->error("Product ID {$productId} not found");
            return;
        }

        for ($i = 1; $i <= $totalStock; $i++) {

            DB::table('stocks')->insert([
                'product_id' => $product->id,
                'lot_number' => 'LOT-' . strtoupper(uniqid()),
                'expired' => Carbon::now()
                    ->addDays(rand(-60, 180)), // expired & future
                'qty' => rand(10, 200),
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info("{$totalStock} stocks created for product {$product->name}");
    }
}
