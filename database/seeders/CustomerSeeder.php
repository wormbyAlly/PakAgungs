<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('customers')->insert([
            [
                'name' => 'PT Medika Farma',
                'email' => 'medika@example.com',
                'phone' => '081234567890',
            ],
            [
                'name' => 'CV Mitra Husada',
                'email' => 'mitra@example.com',
                'phone' => '085200300400',
            ],
        ]);
    }
}
