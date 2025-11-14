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
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: "X IPA 1"
            $table->foreignId('major_id')->constrained()->onDelete('cascade'); // relasi ke jurusan
            $table->unsignedTinyInteger('grade_level'); // 10, 11, 12
            $table->foreignId('academic_year_id')->nullable()->constrained()->onDelete('cascade'); // opsional, tahun ajaran aktif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_classes');
    }
};
