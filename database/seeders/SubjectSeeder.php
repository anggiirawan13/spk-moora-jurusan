<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    public function run()
    {
        DB::table('subjects')->insert([
            ['code' => 'MTK', 'name' => 'Matematika', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'BING', 'name' => 'Bahasa Inggris', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'FIS', 'name' => 'Fisika', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'EKO', 'name' => 'Ekonomi', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'PENJ', 'name' => 'Penjualan', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}