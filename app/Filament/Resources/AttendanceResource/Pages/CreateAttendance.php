<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Models\Student;
use App\Models\Attendance;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\AttendanceResource;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function handleRecordCreation(array $data): Attendance
    {
        $classRoomId = $data['class_room_id'];
        $date = $data['date'];
        $students = $data['students'] ?? [];

        // ✅ SIMPAN KELAS TERAKHIR KE SESSION
        session()->put('attendance_class_room_id', $classRoomId);

        // Jika students kosong, ambil dari database
        if (empty($students)) {
            $students = Student::where('class_room_id', $classRoomId)
                ->orderBy('name')
                ->get(['id', 'name', 'nre'])
                ->map(fn ($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'nre' => $s->nre,
                    'status' => 'presente',
                ])
                ->toArray();
        }

        foreach ($students as $studentRow) {
            if (!isset($studentRow['id'])) {
                continue;
            }

            Attendance::updateOrCreate(
                [
                    'student_id' => $studentRow['id'],
                    'class_room_id' => $classRoomId,
                    'date' => $date,
                ],
                [
                    'status' => $studentRow['status'] ?? 'presente',
                ]
            );
        }

        // Kembalikan satu record (wajib oleh Filament)
        return Attendance::where('class_room_id', $classRoomId)
            ->where('date', $date)
            ->first() ?? new Attendance();
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Absensi Berhasil Disimpan';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
