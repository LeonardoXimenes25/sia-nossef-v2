<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Model;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;
    protected static ?string $title = 'Kria Tinan Letivo';

    /**
     * Override handleRecordCreation untuk menyimpan multiple records
     */
    protected function handleRecordCreation(array $data): Model
    {
        // Extract students data
        $students = $data['students'] ?? [];
        
        // Remove students from data array
        unset($data['students']);
        
        // Simpan attendance untuk setiap student
        $attendanceRecords = [];
        foreach ($students as $student) {
            $attendanceRecords[] = Attendance::create([
                'academic_year_id' => $data['academic_year_id'],
                'period_id' => $data['period_id'],
                'class_room_id' => $data['class_room_id'],
                'subject_assignment_id' => $data['subject_assignment_id'],
                'student_id' => $student['id'],
                'status' => $student['status'],
                'date' => $data['date'],
            ]);
        }
        
        // Return record pertama (untuk redirect)
        return $attendanceRecords[0] ?? new Attendance();
    }

    /**
     * Override redirect after create
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Success notification message
     */
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Absénsia rejistu ho suksesu';
    }
}