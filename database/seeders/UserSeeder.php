<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ADMIN DEFAULT
        DB::table('users')->insert([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // USER DEFAULT
        DB::table('users')->insert([
            'name' => 'User Demo',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        for ($i = 1; $i <= 21; $i++) {
            DB::table('users')->insert([
                'name' => "User Demo {$i}",
                'email' => "user{$i}@example.com",
                'password' => Hash::make('password'), // password sama semua
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

    }
}
