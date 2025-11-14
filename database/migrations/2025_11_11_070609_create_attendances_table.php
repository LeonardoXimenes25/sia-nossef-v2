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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_room_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('subject_assignment_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('academic_year_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('period_id')->nullable()->constrained()->onDelete('set null');
            $table->date('date');
            $table->enum('status', ['presente', 'moras', 'lisensa', 'falta'])->default('presente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
