<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\JenisCoa;

class JenisCoaSeeder extends Seeder
{
    public function run()
    {
        $aset = JenisCoa::create([
            'code' => '1',
            'nama' => 'ASET',
        ]);

        $asetLancar = JenisCoa::create([
            'code' => '1.1',
            'nama' => 'ASET LANCAR',
            'induk_id' => $aset->id,
        ]);

        $kas = JenisCoa::create([
            'code' => '1.1.1',
            'nama' => 'KAS',
            'induk_id' => $asetLancar->id,
        ]);

        $piutang = JenisCoa::create([
            'code' => '1.1.2',
            'nama' => 'PIUTANG',
            'induk_id' => $asetLancar->id,
        ]);

        $pendapatan = JenisCoa::create([
            'code' => '4',
            'nama' => 'PENDAPATAN',
        ]);

        $penjualan = JenisCoa::create([
            'code' => '4.1',
            'nama' => 'PENJUALAN',
            'induk_id' => $pendapatan->id,
        ]);

        $kontra = JenisCoa::create([
            'code' => '4.9',
            'nama' => 'KONTRA PENDAPATAN',
            'induk_id' => $pendapatan->id,
        ]);

        $diskon = JenisCoa::create([
            'code' => '4.9.1',
            'nama' => 'DISKON PENJUALAN',
            'induk_id' => $kontra->id,
        ]);
    }
}
