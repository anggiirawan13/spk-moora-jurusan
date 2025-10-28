<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            AdminSeeder::class,
            CriteriaSeeder::class,
            SubCriteriaSeeder::class,
            SubjectSeeder::class,
            MajorSeeder::class,
            StudentSeeder::class,
            AlternativeSeeder::class,
        ]);
    }
}
