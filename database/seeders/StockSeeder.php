<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 21; $i++) {
            DB::table('stocks')->insert([
                'product_id' => $i,
                'lot_number' => "LOT-00{$i}-A",
                'expired'    => Carbon::now()->addMonths(6),
                'qty'        => 200,
                'user_id'    => 1,
            ]);
        }
    }
}
