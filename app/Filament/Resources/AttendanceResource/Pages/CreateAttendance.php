<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Models\Student;
use Filament\Resources\Pages\CreateRecord;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function handleRecordCreation(array $data): Attendance
    {
        $classRoomId = $data['class_room_id'];
        $date = $data['date'];
        $students = $data['students'] ?? [];

        \Log::info('Creating attendance:', [
            'class_room_id' => $classRoomId,
            'date' => $date,
            'students_count' => count($students),
            'students_data' => $students
        ]);

        // Jika students kosong, ambil dari database dengan NRE
        if (empty($students)) {
            $students = Student::where('class_room_id', $classRoomId)
                ->orderBy('name')
                ->get(['id', 'name', 'nre']) // TAMBAH nre di sini
                ->map(fn($s) => [
                    'id' => $s->id, 
                    'name' => $s->name,
                    'nre' => $s->nre, // TAMBAH nre di sini
                    'status' => 'presente'
                ])
                ->toArray();
        }

        foreach ($students as $studentRow) {
            if (!isset($studentRow['id'])) continue;

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

        // Return first record untuk memenuhi return type
        return Attendance::where('class_room_id', $classRoomId)
            ->where('date', $date)
            ->first() ?? new Attendance();
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Absensi Berhasil';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}