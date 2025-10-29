<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AlternativeSeeder extends Seeder
{
    public function run(): void
    {
        $studentIds = DB::table('students')->pluck('id');

        $criteriaIds = DB::table('criterias')
            ->join('sub_criterias', 'criterias.id', '=', 'sub_criterias.criteria_id')
            ->distinct()
            ->pluck('criterias.id');

        if ($criteriaIds->isEmpty()) {
            echo "Peringatan: Tidak ada Kriteria dengan Sub Kriteria. Tidak ada Alternative Value yang dibuat.\n";
            return;
        }

        foreach ($studentIds as $studentId) {
            try {
                $alternativeId = DB::table('alternatives')->insertGetId([
                    'student_id' => $studentId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                continue;
            }

            foreach ($criteriaIds as $criteriaId) {
                $subCriteria = DB::table('sub_criterias')
                    ->where('criteria_id', $criteriaId)
                    ->inRandomOrder()
                    ->first();

                if (!$subCriteria) continue;

                DB::table('alternative_values')->insert([
                    'alternative_id'  => $alternativeId,
                    'sub_criteria_id' => $subCriteria->id,
                    'value'           => $subCriteria->value,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }
    }
}
