<?php

namespace Database\Seeders;

use App\Models\Major;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $majorTKJ = Major::where('name', 'Teknik Komputer dan Jaringan (TKJ)')->first();
        $majorDKV = Major::where('name', 'Desain Komunikasi Visual (DKV)')->first();
        $majorTG = Major::where('name', 'Teknik Grafika (TG)')->first();

        $subjectMTK = Subject::where('code', 'MTK')->first();
        $subjectFIS = Subject::where('code', 'FIS')->first();
        $subjectBING = Subject::where('code', 'BING')->first();
        $subjectKOMJAR = Subject::where('code', 'KOMJAR')->first();
        $subjectKDG = Subject::where('code', 'KDG')->first();
        $subjectPPC = Subject::where('code', 'PPC')->first();

        if (!$majorTKJ || !$majorDKV || !$majorTG || !$subjectMTK || !$subjectFIS || !$subjectBING || !$subjectKOMJAR || !$subjectKDG || !$subjectPPC) {
            echo "⚠️ Peringatan: Data master (Major, Subject, atau Kode) tidak ditemukan. Pastikan MajorSeeder dan SubjectSeeder sudah dijalankan dengan benar.\n";
            return;
        }

        $criterias = [];

        $criterias[] = [
            'major_id'       => $majorTKJ->id,
            'subject_id'     => $subjectKOMJAR->id,
            'weight'         => 0.35,
            'attribute_type' => 'Benefit',
            'created_at'     => now(),
            'updated_at' => now(),
        ];

        $criterias[] = [
            'major_id'       => $majorTKJ->id,
            'subject_id'     => $subjectMTK->id,
            'weight'         => 0.30,
            'attribute_type' => 'Benefit',
            'created_at'     => now(),
            'updated_at' => now(),
        ];

        $criterias[] = [
            'major_id'       => $majorTKJ->id,
            'subject_id'     => $subjectFIS->id,
            'weight'         => 0.20,
            'attribute_type' => 'Benefit',
            'created_at'     => now(),
            'updated_at' => now(),
        ];

        $criterias[] = [
            'major_id'       => $majorTKJ->id,
            'subject_id'     => $subjectBING->id,
            'weight'         => 0.15,
            'attribute_type' => 'Benefit',
            'created_at'     => now(),
            'updated_at' => now(),
        ];

        $criterias[] = [
            'major_id'       => $majorDKV->id,
            'subject_id'     => $subjectKDG->id,
            'weight'         => 0.40,
            'attribute_type' => 'Benefit',
            'created_at'     => now(),
            'updated_at' => now(),
        ];

        $criterias[] = [
            'major_id'       => $majorDKV->id,
            'subject_id'     => $subjectMTK->id,
            'weight'         => 0.30,
            'attribute_type' => 'Benefit',
            'created_at'     => now(),
            'updated_at' => now(),
        ];

        $criterias[] = [
            'major_id'       => $majorDKV->id,
            'subject_id'     => $subjectBING->id,
            'weight'         => 0.20,
            'attribute_type' => 'Benefit',
            'created_at'     => now(),
            'updated_at' => now(),
        ];

        $criterias[] = [
            'major_id'       => $majorDKV->id,
            'subject_id'     => $subjectFIS->id,
            'weight'         => 0.10,
            'attribute_type' => 'Benefit',
            'created_at'     => now(),
            'updated_at' => now(),
        ];

        $criterias[] = [
            'major_id'       => $majorTG->id,
            'subject_id'     => $subjectPPC->id,
            'weight'         => 0.45,
            'attribute_type' => 'Benefit',
            'created_at'     => now(),
            'updated_at' => now(),
        ];

        $criterias[] = [
            'major_id'       => $majorTG->id,
            'subject_id'     => $subjectFIS->id,
            'weight'         => 0.25,
            'attribute_type' => 'Benefit',
            'created_at'     => now(),
            'updated_at' => now(),
        ];

        $criterias[] = [
            'major_id'       => $majorTG->id,
            'subject_id'     => $subjectMTK->id,
            'weight'         => 0.15,
            'attribute_type' => 'Benefit',
            'created_at'     => now(),
            'updated_at' => now(),
        ];

        $criterias[] = [
            'major_id'       => $majorTG->id,
            'subject_id'     => $subjectBING->id,
            'weight'         => 0.15,
            'attribute_type' => 'Benefit',
            'created_at'     => now(),
            'updated_at' => now(),
        ];

        DB::table('criterias')->insert($criterias);
    }
}
