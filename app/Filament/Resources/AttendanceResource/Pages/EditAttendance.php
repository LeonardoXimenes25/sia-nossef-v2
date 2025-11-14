<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Models\Student;
use Filament\Resources\Pages\EditRecord;

class EditAttendance extends EditRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ambil record yang sedang diedit
        $attendanceRecord = $this->record;
        
        // Ambil semua attendance untuk tanggal dan kelas yang sama
        $classRoomId = $attendanceRecord->class_room_id;
        $date = $attendanceRecord->date;

        // Ambil semua siswa di kelas ini DENGAN NRE
        $students = Student::where('class_room_id', $classRoomId)
            ->orderBy('name')
            ->get(['id', 'name', 'nre']); // TAMBAH nre di sini

        // Ambil semua attendance record untuk tanggal dan kelas ini
        $existingAttendances = Attendance::where('class_room_id', $classRoomId)
            ->where('date', $date)
            ->get()
            ->keyBy('student_id');

        // Format data students dengan status mereka
        $studentsData = [];
        foreach ($students as $student) {
            $attendance = $existingAttendances->get($student->id);
            
            $studentsData[] = [
                'id' => $student->id,
                'nre' => $student->nre, // TAMBAH nre di sini
                'name' => $student->name,
                'status' => $attendance ? $attendance->status : 'presente'
            ];
        }

        $data['students'] = $studentsData;
        $data['class_room_id'] = $classRoomId;
        $data['date'] = $date;

        return $data;
    }

    protected function handleRecordUpdate($record, array $data): Attendance
    {
        $classRoomId = $data['class_room_id'];
        $date = $data['date'];
        $students = $data['students'] ?? [];

        \Log::info('Updating attendance:', [
            'class_room_id' => $classRoomId,
            'date' => $date,
            'students_count' => count($students),
            'students_data' => $students
        ]);

        // Update atau create attendance untuk setiap student
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

        return $record;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Absensi berhasil diperbarui';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}