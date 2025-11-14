<?php

namespace App\Filament\Resources\GradeResource\Pages;

use App\Filament\Resources\GradeResource;
use App\Models\Grade;
use App\Models\Student;
use Filament\Resources\Pages\CreateRecord;

class CreateGrade extends CreateRecord
{
    protected static string $resource = GradeResource::class;

    protected function handleRecordCreation(array $data): Grade
    {
        $classRoomId = $data['class_room_id'];
        $subjectAssignmentId = $data['subject_assignment_id'];
        $academicYearId = $data['academic_year_id'];
        $periodId = $data['period_id'];
        $students = $data['students'] ?? [];

        \Log::info('Creating grades:', [
            'class_room_id' => $classRoomId,
            'subject_assignment_id' => $subjectAssignmentId,
            'students_count' => count($students),
        ]);

        // Buat grade untuk setiap student
        foreach ($students as $studentRow) {
            if (!isset($studentRow['id'])) continue;

            // Auto-generate remarks berdasarkan score
            $score = $studentRow['score'] ?? 0;
            $remarks = GradeResource::getRemarksByScore((float)$score);

            Grade::create([
                'student_id' => $studentRow['id'],
                'class_room_id' => $classRoomId,
                'subject_assignment_id' => $subjectAssignmentId,
                'academic_year_id' => $academicYearId,
                'period_id' => $periodId,
                'score' => $score,
                'remarks' => $remarks, // Gunakan remarks yang auto-generated
            ]);
        }

        // Return first record
        return Grade::where('class_room_id', $classRoomId)
            ->where('subject_assignment_id', $subjectAssignmentId)
            ->where('academic_year_id', $academicYearId)
            ->where('period_id', $periodId)
            ->first() ?? new Grade();
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Valor sira konsege rejistu';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}