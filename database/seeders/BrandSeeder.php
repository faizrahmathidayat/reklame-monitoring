<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    public function run()
    {
        $brands = ['Alfamart', 'Alfamidi', 'Alfaexpress', 'Dan+Dan'];

        foreach ($brands as $nama) {
            DB::table('brands')->insertOrIgnore([
                'nama_brand' => $nama,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
