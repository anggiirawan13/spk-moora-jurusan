<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        $this->call([
            AdminSeeder::class,
            MajorSeeder::class,
            SubjectSeeder::class,
            StudentSeeder::class,
            CriteriaSeeder::class,
            SubCriteriaSeeder::class,
            AlternativeSeeder::class,
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
