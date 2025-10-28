<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubCriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $subCriteria = [
            // C1 - Harga (Cost)
            ['criteria_code' => 'C1', 'name' => '251 – 450 juta', 'value' => 1],
            ['criteria_code' => 'C1', 'name' => '351 – 450 juta', 'value' => 2],
            ['criteria_code' => 'C1', 'name' => '251 – 350 juta', 'value' => 3],
            ['criteria_code' => 'C1', 'name' => '<= 250 juta',    'value' => 4],

            // C2 - Tahun Produksi (Benefit)
            ['criteria_code' => 'C2', 'name' => '2010 – 2014',   'value' => 1],
            ['criteria_code' => 'C2', 'name' => '2015 – 2017',   'value' => 2],
            ['criteria_code' => 'C2', 'name' => '2018 – 2020',   'value' => 3],
            ['criteria_code' => 'C2', 'name' => '>= 2021',        'value' => 4],

            // C3 - Jarak Tempuh (Cost)
            ['criteria_code' => 'C3', 'name' => '100.001 – 150.000 km', 'value' => 1],
            ['criteria_code' => 'C3', 'name' => '50.001 – 100.000 km',  'value' => 2],
            ['criteria_code' => 'C3', 'name' => '20.001 – 50.000 km',   'value' => 3],
            ['criteria_code' => 'C3', 'name' => '<= 20.000 km',          'value' => 4],

            // C4 - Kapasitas Mesin (Benefit)
            ['criteria_code' => 'C4', 'name' => '<= 1000 cc',     'value' => 1],
            ['criteria_code' => 'C4', 'name' => '1001 – 1500',   'value' => 2],
            ['criteria_code' => 'C4', 'name' => '1501 – 2500',   'value' => 3],
            ['criteria_code' => 'C4', 'name' => '> 2500',        'value' => 4],

            // C5 - Kapasitas Penumpang (Benefit)
            ['criteria_code' => 'C5', 'name' => '1 – 2', 'value' => 1],
            ['criteria_code' => 'C5', 'name' => '3 – 7', 'value' => 2],
            ['criteria_code' => 'C5', 'name' => '>= 8',   'value' => 3],

            // C6 - Transmisi (Benefit)
            ['criteria_code' => 'C6', 'name' => 'Manual',   'value' => 1],
            ['criteria_code' => 'C6', 'name' => 'Otomatis', 'value' => 2],

            // C7 - Kelengkapan Fitur (Benefit)
            ['criteria_code' => 'C7', 'name' => 'Fitur umum standar (audio, power steering, alarm, dll)',                          'value' => 1],
            ['criteria_code' => 'C7', 'name' => 'Fitur standar + layar sentuh, kamera mundur, AC double blower, 4 airbag',         'value' => 2],
            ['criteria_code' => 'C7', 'name' => 'Fitur lengkap (touchscreen, kamera 360, keyless, sensor parkir, sunroof)',        'value' => 3],
            ['criteria_code' => 'C7', 'name' => 'Fitur sangat lengkap (semi otonom, adaptive cruise control, blindspot, dll)',     'value' => 4],
            ['criteria_code' => 'C7', 'name' => 'Fitur paling lengkap (ADAS lengkap, surround camera, smart parking)',             'value' => 5],
        ];

        foreach ($subCriteria as $item) {
            $criteria = DB::table('criterias')->where('code', $item['criteria_code'])->first();

            if ($criteria) {
                DB::table('sub_criterias')->insert([
                    'criteria_id' => $criteria->id,
                    'name' => $item['name'],
                    'value' => $item['value'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
