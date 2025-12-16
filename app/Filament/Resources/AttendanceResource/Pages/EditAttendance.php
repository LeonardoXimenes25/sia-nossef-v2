<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Models\Student;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditAttendance extends EditRecord
{
    protected static string $resource = AttendanceResource::class;

    /**
     * =========================
     * PREFILL FORM
     * =========================
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $attendance = $this->record;

        $classRoomId = $attendance->class_room_id;
        $date = $attendance->date;

        // Ambil semua siswa di kelas ini
        $students = Student::where('class_room_id', $classRoomId)
            ->orderBy('name')
            ->get(['id', 'name', 'nre']);

        // Ambil absensi EXISTING hanya untuk kelas & tanggal ini
        $existingAttendances = Attendance::where('class_room_id', $classRoomId)
            ->whereDate('date', $date)
            ->get()
            ->keyBy('student_id');

        // Mapping ke repeater
        $data['students'] = $students->map(function ($student) use ($existingAttendances) {
            return [
                'id'     => $student->id,
                'nre'    => $student->nre,
                'name'   => $student->name,
                'status' => $existingAttendances[$student->id]->status ?? 'presente',
            ];
        })->toArray();

        $data['class_room_id'] = $classRoomId;
        $data['date'] = $date;

        return $data;
    }

    /**
     * =========================
     * SAVE DATA
     * =========================
     */
    protected function handleRecordUpdate($record, array $data): Attendance
    {
        DB::transaction(function () use ($data) {
            $classRoomId = $data['class_room_id'];
            $date = $data['date'];
            $students = $data['students'] ?? [];

            foreach ($students as $studentRow) {
                if (empty($studentRow['id'])) {
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
        });

        return $record;
    }

    /**
     * =========================
     * NOTIFICATION
     * =========================
     */
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Update Susesu';
    }

    /**
     * =========================
     * REDIRECT
     * =========================
     */
    protected function getRedirectUrl(): string
    {
        // Kembali ke index (yang default = hari ini)
        return $this->getResource()::getUrl('index');
    }
}
