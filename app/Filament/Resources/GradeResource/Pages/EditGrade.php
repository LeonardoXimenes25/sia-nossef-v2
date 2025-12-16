<?php

namespace App\Filament\Resources\GradeResource\Pages;

use App\Filament\Resources\GradeResource;
use App\Models\Grade;
use App\Models\Student;
use Filament\Resources\Pages\EditRecord;

class EditGrade extends EditRecord
{
    protected static string $resource = GradeResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $gradeRecord = $this->record;

        $classRoomId = $gradeRecord->class_room_id;
        $subjectAssignmentId = $gradeRecord->subject_assignment_id;
        $academicYearId = $gradeRecord->academic_year_id;
        $periodId = $gradeRecord->period_id;

        // Ambil semua siswa di kelas ini
        $students = Student::where('class_room_id', $classRoomId)
            ->orderBy('name')
            ->get(['id', 'name', 'nre']);

        // Ambil semua grade untuk kombinasi yang sama
        $existingGrades = Grade::where('class_room_id', $classRoomId)
            ->where('subject_assignment_id', $subjectAssignmentId)
            ->where('academic_year_id', $academicYearId)
            ->where('period_id', $periodId)
            ->get()
            ->keyBy('student_id');

        $studentsData = [];
        foreach ($students as $student) {
            $grade = $existingGrades->get($student->id);

            $studentsData[] = [
                'id' => $student->id,
                'nre' => $student->nre,
                'name' => $student->name,
                'score' => $grade?->score ?? null,
                'remarks' => $grade?->remarks ?? GradeResource::getRemarksByScore(0),
            ];
        }

        return [
            'students' => $studentsData,
            'class_room_id' => $classRoomId,
            'subject_assignment_id' => $subjectAssignmentId,
            'academic_year_id' => $academicYearId,
            'period_id' => $periodId,
        ];
    }

    protected function handleRecordUpdate($record, array $data): Grade
    {
        $classRoomId = $data['class_room_id'];
        $subjectAssignmentId = $data['subject_assignment_id'];
        $academicYearId = $data['academic_year_id'];
        $periodId = $data['period_id'];
        $students = $data['students'] ?? [];

        foreach ($students as $studentRow) {
            if (!isset($studentRow['id'])) continue;

            $score = $studentRow['score'] ?? 0;
            $remarks = GradeResource::getRemarksByScore((float)$score);

            Grade::updateOrCreate(
                [
                    'student_id' => $studentRow['id'],
                    'class_room_id' => $classRoomId,
                    'subject_assignment_id' => $subjectAssignmentId,
                    'academic_year_id' => $academicYearId,
                    'period_id' => $periodId,
                ],
                [
                    'score' => $score,
                    'remarks' => $remarks,
                ]
            );
        }

        return $record;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Valor sira konsege atualiza';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
