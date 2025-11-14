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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('teacher_id', 11)->unique();        // Unique teacher ID (sebelumnya NRP)
            $table->string('name', 50);                        // Full name
            $table->enum('gender', ['m', 'f'])->default('m');  // Gender: m = male, f = female
            $table->date('birth_date')->nullable();            // Date of birth
            $table->string('birth_place', 50)->nullable();    // Place of birth

            // Additional fields
            $table->string('educational_qualification', 100)->nullable(); // Academic qualification
            $table->enum('employment_status', ['fp','ft','pt'])->default('fp'); 
            // Employment status: fp = permanent, ft = full-time, pt = part-time
            $table->date('employment_start_date')->nullable(); // Start date of current employment status
            $table->string('phone', 20)->nullable();           // Phone number

            $table->string('email', 100)->nullable();          // Email (optional)
            $table->string('photo', 255)->nullable();          // Profile photo
            $table->timestamps();                               // created_a
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
