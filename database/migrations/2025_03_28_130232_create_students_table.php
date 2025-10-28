<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->unique();
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->unsignedTinyInteger('grade_level');
            $table->foreignId('major_id')->nullable()->constrained('majors')->onUpdate('cascade')->onDelete('cascade'); 
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->string('profile_picture')->nullable();
            $table->smallInteger('is_active')->default(1); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
