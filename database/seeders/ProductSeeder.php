<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [];

        for ($i = 1; $i <= 21; $i++) {
            $products[] = [
                'code'  => 'PRD' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name'  => "Product {$i}",
                'price' => rand(3000, 25000),
            ];
        }

        DB::table('products')->insert($products);
    }
}
