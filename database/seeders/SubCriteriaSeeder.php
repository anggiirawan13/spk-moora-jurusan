<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubCriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $standardScales = [
            ['name' => 'Sangat Baik', 'value' => 5, 'min_value' => 90, 'max_value' => 100],
            ['name' => 'Baik', 'value' => 4, 'min_value' => 70, 'max_value' => 89],
            ['name' => 'Cukup', 'value' => 3, 'min_value' => 50, 'max_value' => 69],
            ['name' => 'Kurang', 'value' => 2, 'min_value' => 30, 'max_value' => 49],
            ['name' => 'Sangat Kurang', 'value' => 1, 'min_value' => 0, 'max_value' => 29],
        ];

        $allCriterias = DB::table('criterias')->pluck('id');

        if ($allCriterias->isEmpty()) {
            echo "Peringatan: Tidak ada data Kriteria (Mata Pelajaran) yang ditemukan. Sub Kriteria tidak dibuat.\n";
            return;
        }

        $dataToInsert = [];
        $now = now();

        foreach ($allCriterias as $criteriaId) {
            foreach ($standardScales as $scale) {
                $dataToInsert[] = [
                    'criteria_id' => $criteriaId,
                    'name'        => $scale['name'],
                    'value'       => $scale['value'],
                    'min_value'       => $scale['min_value'],
                    'max_value'       => $scale['max_value'],
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
        }

        DB::table('sub_criterias')->insert($dataToInsert);
    }
}
