<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WilayahSeeder extends Seeder
{
    public function run()
    {
        $wilayahs = [
            ['kode_wilayah' => 'DC-CBN', 'nama_wilayah' => 'DC Cibinong'],
            ['kode_wilayah' => 'DC-CLS', 'nama_wilayah' => 'DC Cileungsi'],
            ['kode_wilayah' => 'DC-BLR', 'nama_wilayah' => 'DC Balaraja'],
            ['kode_wilayah' => 'DC-CKR', 'nama_wilayah' => 'DC Cikarang'],
            ['kode_wilayah' => 'DC-CJR', 'nama_wilayah' => 'DC Cianjur'],
        ];

        foreach ($wilayahs as $w) {
            DB::table('wilayahs')->insertOrIgnore(array_merge($w, [
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
