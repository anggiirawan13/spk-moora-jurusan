<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubCriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $standardScales = [
            ['name' => 'Sangat Baik', 'value' => 5],
            ['name' => 'Baik', 'value' => 4],
            ['name' => 'Cukup', 'value' => 3],
            ['name' => 'Kurang', 'value' => 2],
            ['name' => 'Sangat Kurang', 'value' => 1],
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
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
        }

        DB::table('sub_criterias')->insert($dataToInsert);
    }
}
