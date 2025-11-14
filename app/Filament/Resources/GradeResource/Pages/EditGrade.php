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
        
        // Ambil semua grade untuk kombinasi yang sama
        $classRoomId = $gradeRecord->class_room_id;
        $subjectAssignmentId = $gradeRecord->subject_assignment_id;
        $academicYearId = $gradeRecord->academic_year_id;
        $periodId = $gradeRecord->period_id;

        // Ambil semua siswa di kelas ini
        $students = Student::where('class_room_id', $classRoomId)
            ->orderBy('name')
            ->get(['id', 'name', 'nre']);

        // Ambil semua grade record untuk kombinasi yang sama
        $existingGrades = Grade::where('class_room_id', $classRoomId)
            ->where('subject_assignment_id', $subjectAssignmentId)
            ->where('academic_year_id', $academicYearId)
            ->where('period_id', $periodId)
            ->get()
            ->keyBy('student_id');

        // Format data students dengan nilai mereka
        $studentsData = [];
        foreach ($students as $student) {
            $grade = $existingGrades->get($student->id);
            
            $studentsData[] = [
                'id' => $student->id,
                'nre' => $student->nre,
                'name' => $student->name,
                'score' => $grade ? $grade->score : 0,
                'remarks' => $grade ? $grade->remarks : GradeResource::getRemarksByScore(0),
            ];
        }

        $data['students'] = $studentsData;
        $data['class_room_id'] = $classRoomId;
        $data['subject_assignment_id'] = $subjectAssignmentId;
        $data['academic_year_id'] = $academicYearId;
        $data['period_id'] = $periodId;

        return $data;
    }

    protected function handleRecordUpdate($record, array $data): Grade
    {
        $classRoomId = $data['class_room_id'];
        $subjectAssignmentId = $data['subject_assignment_id'];
        $academicYearId = $data['academic_year_id'];
        $periodId = $data['period_id'];
        $students = $data['students'] ?? [];

        \Log::info('Updating grades:', [
            'class_room_id' => $classRoomId,
            'subject_assignment_id' => $subjectAssignmentId,
            'students_count' => count($students),
        ]);

        // Hapus semua grade lama untuk kombinasi ini
        Grade::where('class_room_id', $classRoomId)
            ->where('subject_assignment_id', $subjectAssignmentId)
            ->where('academic_year_id', $academicYearId)
            ->where('period_id', $periodId)
            ->delete();

        // Buat grade baru untuk setiap student
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