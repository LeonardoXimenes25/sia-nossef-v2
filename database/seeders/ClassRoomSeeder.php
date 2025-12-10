<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassRoom;
use App\Models\Major;

class ClassRoomSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua major yang sudah ada
        $majors = Major::all()->keyBy('code'); // asumsi kode: 'IPA', 'IPS', dll

        // Contoh data kelas
        $classRooms = [
            ['level' => '10', 'major_code' => 'CSH', 'turma' => 'A'],
            ['level' => '10', 'major_code' => 'CSH', 'turma' => 'B'],
            ['level' => '10', 'major_code' => 'CT', 'turma' => 'A'],
            ['level' => '11', 'major_code' => 'CSH', 'turma' => 'A'],
            ['level' => '11', 'major_code' => 'CSH', 'turma' => 'B'],
            ['level' => '11', 'major_code' => 'CT', 'turma' => 'B'],
            ['level' => '12', 'major_code' => 'CSH', 'turma' => 'A'],
            ['level' => '12', 'major_code' => 'CSH', 'turma' => 'B'],
            ['level' => '12', 'major_code' => 'CT', 'turma' => 'A'],
        ];

        foreach ($classRooms as $cr) {
            if (!isset($majors[$cr['major_code']])) continue; // skip jika major tidak ada

            ClassRoom::create([
                'level' => $cr['level'],
                'major_id' => $majors[$cr['major_code']]->id,
                'turma' => $cr['turma'],
            ]);
        }
    }
}
