<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Student;
use App\Models\Major;
use App\Models\ClassRoom;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    protected array $turmaCounter = [];

    public function model(array $row)
    {
        $nre = trim($row['nre'] ?? '');

        if (!$nre) {
            return null;
        }

        // 🔹 Cari user dari login_id
        $user = User::where('login_id', $nre)->first();
        if (!$user) {
            Log::error("User tidak ditemukan untuk NRE: {$nre}");
            return null;
        }

        // 🔹 Update user dari Excel
        $user->update([
            'name'  => $row['name'],
            'email' => $row['email'] ?? $user->email,
        ]);

        // 🔹 Normalisasi major
        $majorCode = strtoupper(trim($row['major'] ?? ''));

        $majorCode = match ($majorCode) {
            'CT', 'CIENCIA TEKNOLOGIA' => 'CT',
            'CSH', 'CIENCIA SOCIAS NO HUMANIDADE' => 'CSH',
            default => null,
        };

        if (!$majorCode) {
            Log::error("Major tidak valid untuk NRE {$nre}");
            return null;
        }

        $major = Major::where('code', $majorCode)->first();
        if (!$major) {
            Log::error("Major tidak ditemukan: {$majorCode}");
            return null;
        }

        // 🔹 Level
        $level = trim($row['level'] ?? '');
        if (!$level) {
            Log::error("Level kosong untuk NRE {$nre}");
            return null;
        }

        // 🔹 Ambil kelas berdasarkan major + level
        $classRooms = ClassRoom::where('major_id', $major->id)
            ->where('level', $level)
            ->orderBy('id')
            ->get();

        if ($classRooms->isEmpty()) {
            Log::error("ClassRoom tidak ditemukan: {$majorCode} level {$level}");
            return null;
        }

        // 🔹 Bagi otomatis ke turma A/B
        $key = $majorCode . '_' . $level;
        $index = $this->turmaCounter[$key] ?? 0;
        $classRoom = $classRooms[$index % $classRooms->count()];
        $this->turmaCounter[$key] = $index + 1;

        // 🔹 Cegah duplikat
        if (Student::where('user_id', $user->id)->exists()) {
            return null;
        }

        // ✅ SIMPAN STUDENT
        return new Student([
            'user_id'        => $user->id,
            'nre'            => $nre,
            'name'           => $row['name'],
            'sex'            => $row['sex'] ?? 'm',
            'class_room_id'  => $classRoom->id,
            'birth_date'     => $row['birth_date'] ?? null,
            'birth_place'    => $row['birth_place'] ?? null,
            'address'        => $row['address'] ?? null,
            'province'       => $row['province'] ?? null,
            'district'       => $row['district'] ?? null,
            'subdistrict'    => $row['subdistrict'] ?? null,
            'parent_name'    => $row['parent_name'] ?? null,
            'parent_contact' => $row['parent_contact'] ?? null,
            'admission_year' => $row['admission_year'] ?? now()->year,
            'status'         => 'active',
            'photo'          => $row['photo'] ?? null,
        ]);
    }
}
