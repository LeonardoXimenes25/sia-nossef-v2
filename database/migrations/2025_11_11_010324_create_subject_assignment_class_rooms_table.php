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
        Schema::create('subject_assignment_class_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_assignment_id')
                ->constrained('subject_assignments')
                ->onDelete('cascade');
            $table->foreignId('class_room_id')
                ->constrained('class_rooms')
                ->onDelete('cascade');
            $table->timestamps();

            // 🔧 kasih nama unik yang lebih pendek
            $table->unique(['subject_assignment_id', 'class_room_id'], 'subject_assign_class_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_assignment_class_rooms');
    }
};
