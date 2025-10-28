<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MajorSeeder extends Seeder
{
    public function run()
    {
        DB::table('majors')->insert([
            [
                'code' => 'TKJ',
                'name' => 'Teknik Komputer dan Jaringan (TKJ)',
                'description' => 'Fokus pada instalasi, konfigurasi, dan perbaikan perangkat keras dan jaringan komputer.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'AKL',
                'name' => 'Akuntansi dan Keuangan Lembaga (AKL)',
                'description' => 'Fokus pada pencatatan transaksi keuangan, penyusunan laporan keuangan, dan audit.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'BDP',
                'name' => 'Bisnis Daring dan Pemasaran (BDP)',
                'description' => 'Fokus pada strategi pemasaran digital dan pengelolaan bisnis online.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
