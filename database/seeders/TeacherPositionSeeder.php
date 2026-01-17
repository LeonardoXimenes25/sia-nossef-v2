<?php

namespace Database\Seeders;

use App\Models\TeacherPosition;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TeacherPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TeacherPosition::create(
            [
                'name' => 'Diretor',
                'level' => 1
            ]);

        TeacherPosition::create(
            [
                'name' => 'Vice-Diretor',
                'level' => 2
            ]);

        TeacherPosition::create(
            [
                'name' => 'Sekretariu',
                'level' => 3
            ]);

        TeacherPosition::create(
            [
                'name' => 'Tezoreiru',
                'level' => 4
            ]);

        TeacherPosition::create(
            [
                'name' => 'Konsellu Estudante',
                'level' => 5
            ]);

        TeacherPosition::create(
            [
                'name' => 'Professor',
                'level' => 6
            ]);
    }
}
