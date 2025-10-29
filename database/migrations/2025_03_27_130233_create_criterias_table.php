<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('criterias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('major_id')->constrained('majors')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->decimal('weight', 5, 2);
            $table->enum('attribute_type', ['Benefit', 'Cost']);
            $table->unique(['major_id', 'subject_id']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('criterias');
    }
};
