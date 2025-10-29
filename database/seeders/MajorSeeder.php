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
                'code' => 'DKV',
                'name' => 'Desain Komunikasi Visual (DKV)',
                'description' => 'Fokus pada pengembangan keterampilan desain grafis, ilustrasi, fotografi, dan media digital lainnya.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'TG',
                'name' => 'Teknik Grafika (TG)',
                'description' => 'Fokus pada proses cetak, pra-cetak, dan pasca-cetak, termasuk penguasaan mesin cetak dan manajemen produksi grafis.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
