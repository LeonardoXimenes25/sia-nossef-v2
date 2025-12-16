<?php

namespace App\Filament\Resources\GradeResource\Pages;

use App\Filament\Resources\GradeResource;
use App\Models\Grade;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\SubjectAssignment;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms;

class CreateGrade extends CreateRecord
{
    protected static string $resource = GradeResource::class;

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('class_room_id')
                ->label('Klasse / Turma')
                ->options(
                    ClassRoom::with('major')->get()->mapWithKeys(fn ($c) => [
                        $c->id => $c->level . ' ' . $c->turma . ' (' . $c->major->name . ')'
                    ])
                )
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, Forms\Set $set) {
                    if (!$state) {
                        $set('students', []);
                        return;
                    }

                    // Ambil siswa sesuai kelas yang dipilih
                    $students = Student::where('class_room_id', $state)
                        ->orderBy('name')
                        ->get(['id', 'name', 'nre'])
                        ->map(fn($s) => [
                            'id' => $s->id,
                            'nre' => $s->nre,
                            'name' => $s->name,
                            'score' => null,
                            'remarks' => 'Não Avaliado',
                        ])
                        ->toArray();

                    $set('students', $students);
                }),

            Forms\Components\Select::make('subject_assignment_id')
                ->label('Professor / Materia')
                ->options(
                    SubjectAssignment::with(['teacher', 'subject'])
                        ->get()
                        ->mapWithKeys(fn($s) => [
                            $s->id => $s->teacher->name . ' - ' . $s->subject->name
                        ])
                )
                ->searchable()
                ->required(),

            Forms\Components\Select::make('academic_year_id')
                ->label('Tinan Akademiku')
                ->relationship('academicYear', 'name')
                ->required(),

            Forms\Components\Select::make('period_id')
                ->label('Periodu')
                ->relationship('period', 'name')
                ->required(),

            Forms\Components\Section::make('Tabel Estudante sira')
                ->schema([
                    Forms\Components\Repeater::make('students')
                        ->schema([
                            Forms\Components\TextInput::make('nre')->disabled()->dehydrated(false),
                            Forms\Components\TextInput::make('name')->disabled()->dehydrated(false),
                            Forms\Components\TextInput::make('score')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(10)
                                ->step(0.1)
                                ->reactive()
                                ->afterStateUpdated(fn($state, Forms\Set $set) =>
                                    $set('remarks', GradeResource::getRemarksByScore($state))
                                ),
                            Forms\Components\TextInput::make('remarks')->disabled()->dehydrated(false),
                            Forms\Components\Hidden::make('id')->required(),
                        ])
                        ->columns(5)
                        ->addable(false)
                        ->deletable(false)
                        ->reorderable(false)
                        ->columnSpanFull(),
                ]),
        ];
    }

    protected function handleRecordCreation(array $data): Grade
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

        // Return first record sebagai response
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
