<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('class_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('level'); // 10, 11, 12
            $table->foreignId('major_id')->constrained()->cascadeOnDelete(); // IPA / IPS
            $table->string('turma'); // A, B, C
            $table->timestamps();

            $table->unique(['level', 'major_id', 'turma']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_rooms');
    }
};
