<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('alternative_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alternative_id')->constrained('alternatives')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('sub_criteria_id')->constrained('sub_criterias')->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('value', 8, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('alternative_values');
    }
};
