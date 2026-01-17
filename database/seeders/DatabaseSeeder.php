<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\MajorSeeder;
use Database\Seeders\PeriodSeeder;
use Database\Seeders\SubjectSeeder;
use Database\Seeders\ClassRoomSeeder;
use Database\Seeders\AcademicYearSeeder;
use Database\Seeders\TeacherPositionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MajorSeeder::class,
            AcademicYearSeeder::class,
            PeriodSeeder::class,
            ClassRoomSeeder::class,
            TeacherPositionSeeder::class,
            SubjectSeeder::class,
        ]);

        User::factory()->create([
            'login_id' => '12345',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);
    }
}
