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
            ['code' => 'PABP', 'name' => 'Pendidikan Agama & Budi Pekerti', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'BINDO', 'name' => 'Bahasa Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'FIS', 'name' => 'Fisika', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'BASDAT', 'name' => 'Basis Data', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'KOMJAR', 'name' => 'Komputer dan Jaringan Dasar', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'KDG', 'name' => 'Komputer Desain Grafis', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'DKV', 'name' => 'Desain Komunikasi Visual', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'PPC', 'name' => 'Pengolahan Persiapan Cetak', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'TPG', 'name' => 'Teknik Produksi Grafika', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
