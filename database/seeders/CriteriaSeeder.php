<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CriteriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('criterias')->insert([
            [
                'code' => 'C1',
                'name' => 'Harga',
                'weight' => 0.2,
                'attribute_type' => 'cost',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'C2',
                'name' => 'Tahun Produksi',
                'weight' => 0.15,
                'attribute_type' => 'benefit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'C3',
                'name' => 'Jarak Tempuh',
                'weight' => 0.15,
                'attribute_type' => 'cost',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'C4',
                'name' => 'Kapasitas Mesin',
                'weight' => 0.15,
                'attribute_type' => 'benefit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'C5',
                'name' => 'Kapasitas Penumpang',
                'weight' => 0.1,
                'attribute_type' => 'benefit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'C6',
                'name' => 'Transmisi',
                'weight' => 0.1,
                'attribute_type' => 'benefit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'C7',
                'name' => 'Kelengkapan Fitur',
                'weight' => 0.15,
                'attribute_type' => 'benefit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
