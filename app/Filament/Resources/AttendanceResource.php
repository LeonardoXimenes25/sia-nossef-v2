<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\Attendance;
use App\Models\AcademicYear;
use App\Models\Period;
use App\Models\SubjectAssignment;
use App\Models\Teacher;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AttendanceResource\Pages;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use Illuminate\Support\Facades\Auth;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Absensia';
    protected static ?string $pluralModelLabel = 'Absensia';

    /* =========================
     * FORM
     * ========================= */
    public static function form(Form $form): Form
    {
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();
        $activePeriod = Period::where('is_active', true)->first();

        return $form->schema([
            Forms\Components\Select::make('academic_year_id')
                ->label('Ano Akademico')
                ->options(AcademicYear::where('is_active', true)->pluck('name', 'id'))
                ->default($activeAcademicYear?->id)
                ->required()
                ->disabled(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord),

            Forms\Components\Select::make('period_id')
                ->label('Periodo')
                ->options(fn () => Period::where('is_active', true)->pluck('name', 'id'))
                ->default($activePeriod?->id)
                ->required()
                ->disabled(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord),

            Forms\Components\Select::make('class_room_id')
                ->label('Klasse / Turma')
                ->options(fn (Forms\Get $get) => self::getClassRoomOptions(
                    $get('academic_year_id'),
                    $get('period_id')
                ))
                ->live()
                ->required()
                ->disabled(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord)
                ->afterStateUpdated(function ($state, Forms\Set $set) {
                    $set('subject_assignment_id', null);

                    $students = Student::where('class_room_id', $state)
                        ->orderBy('name')
                        ->get(['id', 'name', 'nre'])
                        ->map(fn ($s) => [
                            'id' => $s->id,
                            'nre' => $s->nre,
                            'name' => $s->name,
                            'status' => 'presente',
                            'is_presente' => true,
                            'is_moras' => false,
                            'is_lisensa' => false,
                            'is_falta' => false,
                        ])
                        ->toArray();

                    $set('students', $students);
                }),

            Forms\Components\Select::make('subject_assignment_id')
                ->label('Disciplina')
                ->options(fn (Forms\Get $get) => self::getDisciplinaOptions(
                    $get('academic_year_id'),
                    $get('period_id'),
                    $get('class_room_id')
                ))
                ->live()
                ->required()
                ->disabled(fn (Forms\Get $get, $livewire) => !$get('class_room_id') || $livewire instanceof \Filament\Resources\Pages\EditRecord)
                ->helperText('Hili Klasse/Turma molok'),

            Forms\Components\Select::make('student_id')
                ->label('Estudante')
                ->relationship('student', 'name')
                ->searchable()
                ->required()
                ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord)
                ->disabled(),

            Forms\Components\DatePicker::make('date')
                ->label('Data')
                ->default(now())
                ->required(),

            Forms\Components\Select::make('status')
                ->label('Estatutu')
                ->options([
                    'presente' => 'Presente',
                    'moras' => 'Moras',
                    'lisensa' => 'Lisensa',
                    'falta' => 'Falta',
                ])
                ->required()
                ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord),

            Forms\Components\Repeater::make('students')
                ->schema([
                    Forms\Components\TextInput::make('nre')->label('NRE')->disabled()->dehydrated(false)->columnSpan(1),
                    Forms\Components\TextInput::make('name')->label('Naran')->disabled()->dehydrated(false)->columnSpan(2),
                    Forms\Components\Checkbox::make('is_presente')->label('P')->inline()->reactive()->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $set('is_moras', false);
                            $set('is_lisensa', false);
                            $set('is_falta', false);
                            $set('status', 'presente');
                        }
                    })->dehydrated(false)->columnSpan(1),
                    Forms\Components\Checkbox::make('is_moras')->label('M')->inline()->reactive()->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $set('is_presente', false);
                            $set('is_lisensa', false);
                            $set('is_falta', false);
                            $set('status', 'moras');
                        }
                    })->dehydrated(false)->columnSpan(1),
                    Forms\Components\Checkbox::make('is_lisensa')->label('L')->inline()->reactive()->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $set('is_presente', false);
                            $set('is_moras', false);
                            $set('is_falta', false);
                            $set('status', 'lisensa');
                        }
                    })->dehydrated(false)->columnSpan(1),
                    Forms\Components\Checkbox::make('is_falta')->label('F')->inline()->reactive()->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $set('is_presente', false);
                            $set('is_moras', false);
                            $set('is_lisensa', false);
                            $set('status', 'falta');
                        }
                    })->dehydrated(false)->columnSpan(1),
                    Forms\Components\Hidden::make('status')->default('presente')->required(),
                    Forms\Components\Hidden::make('id')->required(),
                ])
                ->columns(8)
                ->addable(false)
                ->deletable(false)
                ->reorderable(false)
                ->defaultItems(0)
                ->visible(fn (Forms\Get $get, $livewire) => filled($get('class_room_id')) && $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                ->columnSpanFull()
                ->label('Estudante sira'),
        ]);
    }

    /* =========================
     * TABLE
     * ========================= */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.nre')->label('NRE')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('student.name')->label('Naran Estudante')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('classRoom.level')->label('Klasse'),
                Tables\Columns\TextColumn::make('classRoom.turma')->label('Turma'),
                Tables\Columns\TextColumn::make('classRoom.major.code')->label('Area Estudu'),
                Tables\Columns\TextColumn::make('subjectAssignment.subjects.name')->label('Disciplina')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('subjectAssignment.teacher.name')->label('Professor')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('status')->badge()->colors([
                    'success' => 'presente',
                    'warning' => 'moras',
                    'info' => 'lisensa',
                    'danger' => 'falta',
                ]),
                Tables\Columns\TextColumn::make('date')->label('Data')->date('d M Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('Ano Akademico')
                    ->options(AcademicYear::where('is_active', true)->pluck('name', 'id'))
                    ->default(fn () => AcademicYear::where('is_active', true)->first()?->id)
                    ->searchable()
                    ->preload()
                    ->visible(fn () => !Auth::user()?->hasRole('estudante')),

                Tables\Filters\SelectFilter::make('period_id')
                    ->label('Periodo')
                    ->options(Period::pluck('name', 'id'))
                    ->default(fn () => Period::where('is_active', true)->first()?->id)
                    ->searchable()
                    ->preload()
                    ->visible(fn () => !Auth::user()?->hasRole('estudante')),

                Tables\Filters\SelectFilter::make('class_room_id')
                    ->label('Klasse / Turma')
                    ->options(fn () => self::getTableClassRoomOptions())
                    ->searchable()
                    ->preload()
                    ->visible(fn () => !Auth::user()?->hasRole('estudante')),

                Tables\Filters\SelectFilter::make('subject_assignment_id')
                    ->label('Disciplina')
                    ->options(fn () => self::getTableDisciplinaOptions())
                    ->searchable()
                    ->preload()
                    ->visible(fn () => !Auth::user()?->hasRole('estudante')),

                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('date')->label('Data')->default(now())->required()
                    ])
                    ->query(fn (Builder $query, array $data) => $query->whereDate('date', $data['date']))
                    ->visible(fn () => !Auth::user()?->hasRole('estudante')),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'presente' => 'Presente',
                        'moras' => 'Moras',
                        'lisensa' => 'Lisensa',
                        'falta' => 'Falta',
                    ])
                    ->multiple()
                    ->visible(fn () => !Auth::user()?->hasRole('estudante')),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->fileName('Absencia_' . now()->format('Y-m-d'))
                    ->defaultFormat('pdf'),
            ])
            ->defaultSort('date', 'desc');
    }

    /* =========================
     * OPTIONS TABLE FILTERS
     * ========================= */
    private static function getTableClassRoomOptions(): array
    {
        $user = Auth::user();
        if (!$user) return [];

        if ($user->hasRole('super_admin')) {
            return ClassRoom::with('major')->orderBy('level')->orderBy('turma')->get()
                ->mapWithKeys(fn ($class) => [$class->id => $class->level . ' ' . $class->turma . ' (' . $class->major->name . ')'])
                ->toArray();
        }

        $teacher = Teacher::where('user_id', $user->id)->first();
        if (!$teacher) return [];

        return SubjectAssignment::where('teacher_id', $teacher->id)->where('is_active', true)
            ->with('classRooms.major')->get()->pluck('classRooms')->flatten()->unique('id')->sortBy('level')
            ->mapWithKeys(fn ($class) => [$class->id => $class->level . ' ' . $class->turma . ' (' . $class->major->name . ')'])
            ->toArray();
    }

    private static function getTableDisciplinaOptions(): array
    {
        $user = Auth::user();
        if (!$user) return [];

        $query = SubjectAssignment::with('subjects', 'teacher')->where('is_active', true);

        if ($user->hasRole('super_admin')) {
            return $query->get()->mapWithKeys(fn ($assignment) => [
                $assignment->id => $assignment->subjects->pluck('name')->join(', ') . ' - ' . $assignment->teacher->name
            ])->toArray();
        }

        $teacher = Teacher::where('user_id', $user->id)->first();
        if (!$teacher) return [];

        $query->where('teacher_id', $teacher->id);
        return $query->get()->mapWithKeys(fn ($assignment) => [
            $assignment->id => $assignment->subjects->pluck('name')->join(', ') . ' - ' . $assignment->teacher->name
        ])->toArray();
    }

    /* =========================
     * OPTIONS FORM
     * ========================= */
    private static function getClassRoomOptions(?int $academicYearId = null, ?int $periodId = null): array
    {
        $user = Auth::user();
        if (!$user) return [];

        if ($user->hasRole('super_admin')) {
            return ClassRoom::with('major')->orderBy('level')->orderBy('turma')->get()
                ->mapWithKeys(fn ($class) => [$class->id => $class->level . ' ' . $class->turma . ' (' . $class->major->name . ')'])
                ->toArray();
        }

        $teacher = Teacher::where('user_id', $user->id)->first();
        if (!$teacher) return [];

        $query = SubjectAssignment::where('teacher_id', $teacher->id)->where('is_active', true);

        if ($academicYearId) $query->where('academic_year_id', $academicYearId);
        if ($periodId) $query->where('period_id', $periodId);

        return $query->with('classRooms.major')->get()->pluck('classRooms')->flatten()->unique('id')->sortBy('level')
            ->mapWithKeys(fn ($class) => [$class->id => $class->level . ' ' . $class->turma . ' (' . $class->major->name . ')'])
            ->toArray();
    }

    private static function getDisciplinaOptions(?int $academicYearId, ?int $periodId, ?int $classRoomId): array
    {
        if (!$academicYearId || !$periodId || !$classRoomId) return [];

        $user = Auth::user();
        if (!$user) return [];

        $query = SubjectAssignment::with(['subjects', 'teacher', 'classRooms'])
            ->where('academic_year_id', $academicYearId)
            ->where('period_id', $periodId)
            ->where('is_active', true);

        if ($user->hasRole('super_admin')) {
            $results = $query->get()->filter(fn ($assignment) => $assignment->classRooms->contains('id', $classRoomId));
            return $results->mapWithKeys(fn ($assignment) => [
                $assignment->id => $assignment->subjects->pluck('name')->join(', ') . ' - ' . $assignment->teacher->name
            ])->toArray();
        }

        $teacher = Teacher::where('user_id', $user->id)->first();
        if (!$teacher) return [];

        $query->where('teacher_id', $teacher->id);
        $results = $query->get()->filter(fn ($assignment) => $assignment->classRooms->contains('id', $classRoomId));

        return $results->mapWithKeys(fn ($assignment) => [
            $assignment->id => $assignment->subjects->pluck('name')->join(', ') . ' - ' . $assignment->teacher->name
        ])->toArray();
    }

    /* =========================
     * PAGES
     * ========================= */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }

    /* =========================
     * QUERY BERDASARKAN ROLE
     * ========================= */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if (!$user) return $query->whereRaw('1 = 0');

        // SUPER ADMIN → semua data
        if ($user->hasRole('super_admin')) return $query;

        // ESTUDANTE → hanya data sendiri
        if ($user->hasRole('estudante')) {
            $student = Student::where('user_id', $user->id)->first();
            if (!$student) return $query->whereRaw('1 = 0');
            return $query->where('student_id', $student->id);
        }

        // MESTRE → hanya data miliknya
        $teacher = Teacher::where('user_id', $user->id)->first();
        if (!$teacher) return $query->whereRaw('1 = 0');

        return $query->whereHas('subjectAssignment', fn ($q) => $q->where('teacher_id', $teacher->id));
    }
}
