<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    public function run()
    {
        $tkjMajorId = DB::table('majors')->where('name', 'LIKE', '%TKJ%')->value('id');
        $aklMajorId = DB::table('majors')->where('name', 'LIKE', '%AKL%')->value('id');

        DB::table('students')->insert([
            [
                'nis' => '1012023001',
                'name' => 'Ahmad Raka',
                'email' => 'ahmad@example.com',
                'grade_level' => 10,
                'major_id' => $tkjMajorId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nis' => '1012023002',
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'grade_level' => 10,
                'major_id' => $aklMajorId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}