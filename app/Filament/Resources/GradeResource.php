<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GradeResource\Pages;
use App\Models\Grade;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\AcademicYear;
use App\Models\Period;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;

class GradeResource extends Resource
{
    protected static ?string $model = Grade::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Valor';
    protected static ?string $pluralModelLabel = 'Valor';

    public static function getEloquentQuery(): Builder
    {
        $activeYearId = AcademicYear::where('is_active', true)->value('id');
        $activePeriodId = Period::where('is_active', true)
            ->where('academic_year_id', $activeYearId)
            ->value('id');

        $query = parent::getEloquentQuery()
            ->where('academic_year_id', $activeYearId)
            ->where('period_id', $activePeriodId);

        $user = Auth::user();

        if ($user && $user->hasRole('estudante')) {
            // Siswa hanya melihat nilainya sendiri
            $student = $user->student;
            if ($student) {
                $query->where('student_id', $student->id);
            }
        }

        if ($user && $user->hasRole('mestre')) {
            // Guru hanya melihat nilai siswa yang dia ajar
            $teacher = $user->teacher;
            if ($teacher) {
                $query->where('teacher_id', $teacher->id);
            }
        }

        return $query;
    }

    public static function getRemarksByScore($score): string
    {
        if ($score === null || $score === '') {
            return 'Não Avaliado';
        }

        $score = (float) $score;

        return match (true) {
            $score >= 9.5 => 'Excelente',
            $score >= 8.5 => 'Muito Bom',
            $score >= 7.0 => 'Bom',
            $score >= 6.0 => 'Suficiente',
            $score >= 5.0 => 'Insuficiente',
            $score >= 3.0 => 'Mau',
            $score >= 1.0 => 'Muito Mau',
            default       => 'Não Avaliado',
        };
    }

    public static function form(Form $form): Form
    {
        $activeYearId = AcademicYear::where('is_active', true)->value('id');
        $activePeriodId = Period::where('is_active', true)
            ->where('academic_year_id', $activeYearId)
            ->value('id');

        $isTeacher = Auth::user()?->hasRole('mestre') && Auth::user()?->teacher;

        $formFields = [];

        if ($isTeacher) {
            // Guru
            $formFields[] = Forms\Components\TextInput::make('teacher_id_display')
                ->label('Professor')
                ->default(fn () => Auth::user()?->teacher?->name)
                ->disabled()
                ->dehydrated(false);

            $formFields[] = Select::make('subject_id')
                ->label('Materia')
                ->options(function () {
                    $teacher = Auth::user()?->teacher;
                    if (!$teacher) return [];
                    return $teacher->subjectAssignments()
                        ->with('subjects')
                        ->get()
                        ->pluck('subjects')
                        ->flatten()
                        ->unique('id')
                        ->pluck('name', 'id');
                })
                ->reactive()
                ->required()
                ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('class_room_id', null));

            $formFields[] = Select::make('class_room_id')
                ->label('Klasse / Turma')
                ->searchable()
                ->preload()
                ->nullable()
                ->options(function (Forms\Get $get) {
                    $teacher = Auth::user()?->teacher;
                    if (!$teacher) return [];
                    $subjectId = $get('subject_id');
                    if (!$subjectId) return [];
                    $assignments = \App\Models\SubjectAssignment::where('teacher_id', $teacher->id)
                        ->where('is_active', true)
                        ->with(['subjects', 'classRooms.major'])
                        ->get();
                    $options = [];
                    foreach ($assignments as $assignment) {
                        if ($assignment->classRooms->isNotEmpty()) {
                            $subjects = $assignment->subjects()->get();
                            if ($subjects->where('id', $subjectId)->isNotEmpty()) {
                                foreach ($assignment->classRooms as $classroom) {
                                    $majorName = $classroom->major?->name ?? 'N/A';
                                    $options[$classroom->id] = $classroom->level . ' ' . $classroom->turma . ' | ' . $majorName;
                                }
                            }
                        }
                    }
                    return $options;
                })
                ->reactive()
                ->afterStateUpdated(function ($state, Forms\Set $set) {
                    if (!$state) return $set('students', []);
                    $students = Student::where('class_room_id', $state)
                        ->orderBy('name')
                        ->get(['id','nre','name'])
                        ->map(fn($s) => [
                            'id'=>$s->id,
                            'nre'=>$s->nre,
                            'name'=>$s->name,
                            'score'=>null,
                            'remarks'=>'Não Avaliado',
                        ])->toArray();
                    $set('students', $students);
                });

            $formFields[] = Hidden::make('teacher_id')
                ->default(fn() => Auth::user()->teacher->id)
                ->dehydrated();
        } else {
            // Admin
            $formFields[] = Select::make('class_room_id')
                ->label('Klasse / Turma')
                ->options(ClassRoom::with('major')->get()->mapWithKeys(fn($c)=>[$c->id => "{$c->level} {$c->turma} ({$c->major->name})"]))
                ->reactive()
                ->required()
                ->afterStateUpdated(function ($state, Forms\Set $set) {
                    if (!$state) return $set('students', []);
                    $students = Student::where('class_room_id', $state)
                        ->orderBy('name')
                        ->get(['id','nre','name'])
                        ->map(fn($s)=>[
                            'id'=>$s->id,
                            'nre'=>$s->nre,
                            'name'=>$s->name,
                            'score'=>null,
                            'remarks'=>'Não Avaliado'
                        ])->toArray();
                    $set('students',$students);
                });

            $formFields[] = Select::make('subject_id')
                ->label('Materia')
                ->options(Subject::orderBy('name')->pluck('name','id'))
                ->required();

            $formFields[] = Select::make('teacher_id')
                ->label('Professor')
                ->options(Teacher::orderBy('name')->pluck('name','id'))
                ->nullable();
        }

        $formFields[] = Select::make('academic_year_id')
            ->label('Tinan Akademiku')
            ->options(AcademicYear::orderBy('start_date','desc')->pluck('name','id'))
            ->default($activeYearId)
            ->disabled()
            ->dehydrated()
            ->required();

        $formFields[] = Select::make('period_id')
            ->label('Periodu')
            ->options(Period::where('academic_year_id',$activeYearId)->pluck('name','id'))
            ->default($activePeriodId)
            ->disabled()
            ->dehydrated()
            ->required();

        $formFields[] = Section::make('Tabel Estudante sira')
            ->schema([
                Forms\Components\Repeater::make('students')
                    ->label('Lista Estudante')
                    ->schema([
                        Grid::make(5)->schema([
                            TextInput::make('nre')->disabled()->dehydrated(false)->label('NRE'),
                            TextInput::make('name')->disabled()->dehydrated(false)->label('Naran Estudante'),
                            TextInput::make('score')
                                ->label('Valor')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(10)
                                ->step(0.1)
                                ->reactive()
                                ->afterStateUpdated(fn($state, Forms\Set $set)=>$set('remarks', self::getRemarksByScore($state))),
                            TextInput::make('remarks')->label('Observasaun')->disabled()->dehydrated(false),
                            Hidden::make('id')->required(),
                        ]),
                    ])
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false)
                    ->columnSpanFull()
                    ->default([]),
            ]);

        return $form->schema($formFields);
    }

    public static function table(Table $table): Table
    {
        $user = Auth::user();
        $isStudent = $user?->hasRole('estudante') ?? false;
        $isTeacher = $user?->hasRole('mestre') ?? false;

        $tableInstance = $table
            ->columns([
                Tables\Columns\TextColumn::make('student.nre')->label('NRE')->searchable(!$isStudent),
                Tables\Columns\TextColumn::make('student.name')->label('Naran Estudante')->searchable(!$isStudent),
                Tables\Columns\TextColumn::make('classRoom.level')->label('Klasse'),
                Tables\Columns\TextColumn::make('classRoom.turma')->label('Turma'),
                Tables\Columns\TextColumn::make('classRoom.major.code')->label('Area Estudu'),
                Tables\Columns\TextColumn::make('subject.name')->label('Disiplina'),
                Tables\Columns\TextColumn::make('teacher.name')->label('Professor'),
                Tables\Columns\TextColumn::make('score')->label('Valor')->badge()
                    ->color(fn($state)=>match(true){
                        $state >= 8.5 => 'success',
                        $state >= 7.0 => 'warning',
                        $state >= 6.0 => 'info',
                        $state > 0 => 'danger',
                        default => 'gray'
                    }),
                Tables\Columns\TextColumn::make('remarks')->label('Observasaun')->badge(),
                Tables\Columns\TextColumn::make('academicYear.name')->label('Tinan Akademiku'),
                Tables\Columns\TextColumn::make('period.name')->label('Periodu'),
                Tables\Columns\TextColumn::make('created_at')->date('d M Y')->label('Data Kriasaun'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions($isStudent ? [] : [
                FilamentExportHeaderAction::make('export')
                    ->label('Exporta PDF')
                    ->fileName('Grades_' . now()->format('Y-m-d_H-i-s'))
                    ->color('success'),
            ])
            ->defaultSort('created_at','desc');

        // Hanya tampilkan filter jika bukan estudante
        if (!$isStudent) {
            $tableInstance->filters([
                Tables\Filters\SelectFilter::make('class_room_id')
                    ->label('Klasse / Turma')
                    ->options(function(){
                        $user = Auth::user();
                        if($user && $user->hasRole('mestre')){
                            $teacher = $user->teacher;
                            if($teacher){
                                return $teacher->subjectAssignments()
                                    ->where('is_active',true)
                                    ->with('classRooms','classRooms.major')
                                    ->get()
                                    ->flatMap(fn($assignment)=>$assignment->classRooms)
                                    ->unique('id')
                                    ->mapWithKeys(fn($c)=>[$c->id => "{$c->level} {$c->turma} ({$c->major->name})"])
                                    ->toArray();
                            }
                        }
                        return ClassRoom::with('major')->get()->mapWithKeys(fn($c)=>[$c->id => "{$c->level} {$c->turma} ({$c->major->name})"])->toArray();
                    })
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('Disiplina')
                    ->options(function(){
                        $user = Auth::user();
                        if($user && $user->hasRole('mestre')){
                            $teacher = $user->teacher;
                            if($teacher){
                                return Subject::whereHas('subjectAssignments', fn($q)=>$q->where('teacher_id',$teacher->id))
                                    ->orderBy('name')
                                    ->pluck('name','id')
                                    ->toArray();
                            }
                        }
                        return Subject::orderBy('name')->pluck('name','id')->toArray();
                    })
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('teacher_id')
                    ->label('Professor')
                    ->options(function(){
                        return Teacher::orderBy('name')->pluck('name','id')->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->hidden(fn()=>Auth::user()?->hasRole('mestre') ?? false),
            ])->filtersLayout(Tables\Enums\FiltersLayout::AboveContent);
        }

        return $tableInstance;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGrades::route('/'),
            'create' => Pages\CreateGrade::route('/create'),
            'edit' => Pages\EditGrade::route('/{record}/edit'),
        ];
    }
}
