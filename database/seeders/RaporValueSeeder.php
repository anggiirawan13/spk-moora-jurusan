<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Subject;
use App\Models\RaporValue;
use App\Models\Major;

class RaporValueSeeder extends Seeder
{
    public function run(): void
    {
        $raka = Student::where('nis', '1012023001')->first();
        $budi = Student::where('nis', '1012023002')->first();

        $mtkWajib = Subject::where('code', 'MTK')->first();
        $fisika = Subject::where('code', 'FIS')->first();
        $geografi = Subject::where('code', 'DKV')->first();

        if (!$budi || !$raka || !$mtkWajib || !$fisika || !$geografi) {
            $this->command->error("Pastikan SetupSeeder dan TestSetupSeeder sudah dijalankan terlebih dahulu!");
            return;
        }

        RaporValue::create([
            'student_id' => $budi->id,
            'subject_id' => $mtkWajib->id,
            'value' => 95.00,
        ]);

        RaporValue::create([
            'student_id' => $budi->id,
            'subject_id' => $fisika->id,
            'value' => 82.50,
        ]);

        RaporValue::create([
            'student_id' => $raka->id,
            'subject_id' => $mtkWajib->id,
            'value' => 75.00,
        ]);

        RaporValue::create([
            'student_id' => $raka->id,
            'subject_id' => $geografi->id,
            'value' => 65.00,
        ]);
    }
}
