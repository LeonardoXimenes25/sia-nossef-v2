<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Major;
use App\Models\ClassRoom;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    // Counter per major + level untuk membagi turma A/B
    protected $turmaCounter = [];

    public function model(array $row)
    {
        // Skip baris kosong minimal NRE & Name
        if (empty($row['nre']) || empty($row['name'])) {
            return null;
        }

        // Ambil kode major dari Excel
        $majorCode = strtoupper(trim($row['major'] ?? ''));

        // Normalisasi sesuai bahasa lokal
        switch ($majorCode) {
            case 'CT':
            case 'CIENCIA TEKNOLOGIA':
                $majorCode = 'CT';
                break;
            case 'CSH':
            case 'CIENCIA SOCIAS NO HUMANIDADE':
                $majorCode = 'CSH';
                break;
            default:
                Log::warning('Major code unknown: ' . $majorCode);
                return null;
        }

        // Cari major di DB
        $major = Major::where('code', $majorCode)->first();
        if (!$major) {
            Log::warning('Major not found: ' . $majorCode);
            return null;
        }

        // Ambil level, default 10
        $level = trim($row['level'] ?? '10');

        // Ambil semua class_room untuk major + level
        $classRooms = ClassRoom::where('major_id', $major->id)
            ->where('level', $level)
            ->orderBy('id')
            ->get();

        if ($classRooms->isEmpty()) {
            Log::warning("ClassRoom not found for major {$major->name} level {$level}");
            return null;
        }

        // Tentukan index untuk merata ke turma A/B
        $key = $majorCode . '_' . $level;
        $index = $this->turmaCounter[$key] ?? 0;

        // Ambil class_room sesuai index (bergantian A/B)
        $classRoom = $classRooms[$index % $classRooms->count()];

        // Update counter
        $this->turmaCounter[$key] = $index + 1;

        // Buat student
        return new Student([
            'nre'            => trim($row['nre']),
            'name'           => trim($row['name']),
            'sex'            => strtolower(trim($row['sex'] ?? 'm')),
            'major_id'       => $major->id,
            'class_room_id'  => $classRoom->id,
            'birth_date'     => $row['birth_date'] ?? null,
            'birth_place'    => trim($row['birth_place'] ?? ''),
            'address'        => trim($row['address'] ?? ''),
            'province'       => trim($row['province'] ?? ''),
            'district'       => trim($row['district'] ?? ''),
            'subdistrict'    => trim($row['subdistrict'] ?? ''),
            'parent_name'    => trim($row['parent_name'] ?? ''),
            'parent_contact' => trim($row['parent_contact'] ?? ''),
            'admission_year' => trim($row['admission_year'] ?? date('Y')),
            'status'         => 'active',
        ]);
    }
}
