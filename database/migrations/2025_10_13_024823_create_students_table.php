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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nre', 11)->unique(); // Nomor unik siswa
            $table->string('name', 50);
            $table->enum('sex', ['m', 'f'])->default('m');

            $table->foreignId('class_room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('major_id')->constrained()->cascadeOnDelete();

            // Field tambahan
            $table->date('birth_date')->nullable();           // Tanggal lahir
            $table->string('birth_place', 50)->nullable();   // Tempat lahir
            $table->text('address')->nullable();             // Alamat lengkap
            $table->string('province', 50)->nullable();      // Provinsi / Kabupaten
            $table->string('district', 50)->nullable();      // Kecamatan / Distrik
            $table->string('subdistrict', 50)->nullable();   // Desa / Subdistrict
            $table->string('parent_name', 50)->nullable();   // Nama orang tua / wali
            $table->string('parent_contact', 20)->nullable();// No HP orang tua
            $table->year('admission_year')->nullable();      // Tahun masuk sekolah
            $table->enum('status', ['active','alumni','left'])->default('active'); // Status siswa
            $table->string('photo')->nullable();             // Foto profil siswa
            $table->string('email')->nullable();             // Email siswa (opsional)
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
