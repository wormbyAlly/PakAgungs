<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Coa;
use App\Models\JenisCoa;

class CoaSeeder extends Seeder
{
    public function run()
    {
        $kasJenis = JenisCoa::where('code', '1.1.1')->first();
        $piutangJenis = JenisCoa::where('code', '1.1.2')->first();
        $penjualanJenis = JenisCoa::where('code', '4.1')->first();
        $diskonJenis = JenisCoa::where('code', '4.9.1')->first();

        Coa::insert([
            [
                'code' => '101',
                'nama' => 'Kas Besar',
                'jenis_coas_id' => $kasJenis->id,
            ],
            [
                'code' => '102',
                'nama' => 'Piutang Usaha',
                'jenis_coas_id' => $piutangJenis->id,
            ],
            [
                'code' => '401',
                'nama' => 'Penjualan Obat & Alkes',
                'jenis_coas_id' => $penjualanJenis->id,
            ],
            [
                'code' => '402',
                'nama' => 'Diskon Penjualan',
                'jenis_coas_id' => $diskonJenis->id,
            ],
        ]);
    }
}
