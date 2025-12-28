<?php

namespace App\Filament\Resources\GradeResource\Pages;

use App\Filament\Resources\GradeResource;
use App\Models\Grade;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\AcademicYear;
use App\Models\Period;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms;

class CreateGrade extends CreateRecord
{
    protected static string $resource = GradeResource::class;
    protected static ?string $title = 'Kria Valor';

    protected function getFormSchema(): array
    {
        $activeYearId = AcademicYear::where('is_active', true)->value('id');
        $activePeriodId = Period::where('is_active', true)
            ->where('academic_year_id', $activeYearId)
            ->value('id');

        return [
            Forms\Components\Select::make('class_room_id')
                ->label('Klasse / Turma')
                ->options(ClassRoom::with('major')->get()->mapWithKeys(fn ($c) => [
                    $c->id => "{$c->level} {$c->turma} ({$c->major->name})"
                ]))
                ->reactive()
                ->required()
                ->afterStateUpdated(function ($state, Forms\Set $set) {
                    if (!$state) {
                        $set('students', []);
                        return;
                    }

                    $students = Student::where('class_room_id', $state)
                        ->orderBy('name')
                        ->get(['id', 'nre', 'name'])
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

            Forms\Components\Select::make('subject_id')
                ->label('Materia')
                ->options(Subject::orderBy('name')->pluck('name', 'id'))
                ->required(),

            Forms\Components\Select::make('teacher_id')
                ->label('Professor')
                ->options(Teacher::orderBy('name')->pluck('name', 'id'))
                ->nullable(),

            Forms\Components\Select::make('academic_year_id')
                ->label('Tinan Akademiku')
                ->default($activeYearId)
                ->disabled(),

            Forms\Components\Select::make('period_id')
                ->label('Periodu')
                ->default($activePeriodId)
                ->disabled(),

            Forms\Components\Section::make('Tabel Estudante sira')
                ->schema([
                    Forms\Components\Repeater::make('students')
                        ->schema([
                            Forms\Components\Grid::make(5)->schema([
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
                            ]),
                        ])
                        ->addable(false)
                        ->deletable(false)
                        ->reorderable(false)
                        ->columnSpanFull()
                        ->default([]),
                ]),
        ];
    }

    protected function handleRecordCreation(array $data): Grade
    {
        $classRoomId = $data['class_room_id'];
        $subjectId = $data['subject_id'];
        $teacherId = $data['teacher_id'] ?? null;
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
                    'subject_id' => $subjectId,
                    'teacher_id' => $teacherId,
                    'academic_year_id' => $academicYearId,
                    'period_id' => $periodId,
                ],
                [
                    'score' => $score,
                    'remarks' => $remarks,
                ]
            );
        }

        return Grade::where('class_room_id', $classRoomId)
            ->where('subject_id', $subjectId)
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
