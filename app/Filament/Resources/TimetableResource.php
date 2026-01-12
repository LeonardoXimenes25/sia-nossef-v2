<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Period;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Models\Timetable;
use App\Models\AcademicYear;
use Filament\Resources\Resource;
use App\Models\SubjectAssignment;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TimetableResource\Pages;
use Illuminate\Support\Facades\Auth;

class TimetableResource extends Resource
{
    protected static ?string $model = Timetable::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Horariu';

    /**
     * Days of week mapping
     */
    protected static array $dayLabels = [
        'Monday' => 'Segunda',
        'Tuesday' => 'Tersa',
        'Wednesday' => 'Kuarta',
        'Thursday' => 'Kinta',
        'Friday' => 'Sexta',
        'Saturday' => 'Sabadu',
    ];

    public static function getEloquentQuery(): Builder
    {
        // Get the active academic year and period
        $activeYear = AcademicYear::where('is_active', true)->first();
        $activePeriod = Period::where('is_active', true)
            ->where('academic_year_id', $activeYear?->id)
            ->first();

        return parent::getEloquentQuery()
            ->with([
                'subjectAssignment.teacher',
                'subject',
                'classRoom.major',
                'academicYear',
                'period',
            ])
            ->where('academic_year_id', $activeYear?->id)
            ->where('period_id', $activePeriod?->id);
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        $activeYearId = AcademicYear::where('is_active', true)->value('id');
        $activePeriodId = Period::where('is_active', true)
            ->where('academic_year_id', $activeYearId)
            ->value('id');

        // Check if logged-in user is a teacher
        $user = Auth::user();
        $isTeacher = $user && $user->teacher;
        $teacherId = $isTeacher ? $user->teacher->id : null;

        return $form->schema([
            Forms\Components\Select::make('academic_year_id')
                ->label('Tinan Akademiku')
                ->options(AcademicYear::pluck('name','id')->toArray())
                ->default($activeYearId)
                ->disabled()
                ->required(),

            Forms\Components\Select::make('period_id')
                ->label('Periodu')
                ->options(Period::where('academic_year_id', $activeYearId)->pluck('name','id')->toArray())
                ->default($activePeriodId)
                ->disabled()
                ->required(),

            Forms\Components\Select::make('subject_assignment_id')
                ->label('Profesor')
                ->options(fn() => 
                    $isTeacher
                        ? SubjectAssignment::with('teacher')
                            ->where('is_active', true)
                            ->where('teacher_id', $teacherId)
                            ->get()
                            ->mapWithKeys(fn($sa) => [
                                $sa->id => ($sa->teacher?->name ?? 'N/A')
                            ])
                            ->toArray()
                        : SubjectAssignment::with('teacher')
                            ->where('is_active', true)
                            ->get()
                            ->mapWithKeys(fn($sa) => [
                                $sa->id => ($sa->teacher?->name ?? 'N/A')
                            ])
                            ->toArray()
                )
                ->default(fn() => 
                    $isTeacher 
                        ? $user->teacher->subjectAssignments()
                            ->where('is_active', true)
                            ->first()
                            ?->id
                        : null
                )
                ->disabled($isTeacher)
                ->live()
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\Select::make('subject_id')
                ->label('Disiplina')
                ->options(Subject::pluck('name', 'id')->toArray())
                ->searchable()
                ->required(),

            Forms\Components\Select::make('class_room_id')
                ->label('Klasse / Turma')
                ->options(ClassRoom::with('major')
                    ->get()
                    ->mapWithKeys(fn($cr) => [
                        $cr->id => "{$cr->level} {$cr->turma}" . ($cr->major ? " ({$cr->major->name})" : '')
                    ])
                    ->toArray())
                ->searchable()
                ->required(),

            Forms\Components\Select::make('day')
                ->label('Loron')
                ->options(self::$dayLabels)
                ->required(),

            Forms\Components\TimePicker::make('start_time')->label('Oras Hahu')->required(),
            Forms\Components\TimePicker::make('end_time')->label('Oras Ramata')->required(),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Nu')->sortable(),
                Tables\Columns\TextColumn::make('subjectAssignment.teacher.name')->label('Profesor')->searchable(),
                Tables\Columns\TextColumn::make('subject.name')->label('Disiplina')->searchable(),
                Tables\Columns\TextColumn::make('classRoom.level')->label('Klase'),
                Tables\Columns\TextColumn::make('classRoom.turma')->label('Turma'),
                Tables\Columns\TextColumn::make('classRoom.major.name')->label('Area Estudu'),
                Tables\Columns\TextColumn::make('day')->label('Loron')->formatStateUsing(fn($state) => self::$dayLabels[$state] ?? $state),
                Tables\Columns\TextColumn::make('start_time')->label('Oras Hahu'),
                Tables\Columns\TextColumn::make('end_time')->label('Oras Ramata'),
                Tables\Columns\TextColumn::make('academicYear.name')->label('Tinan Akademiku'),
                Tables\Columns\TextColumn::make('period.name')->label('Periodu'),
                // Tables\Columns\TextColumn::make('subjectAssignment.is_active')->label('Status Profesor')
                //     ->getStateUsing(fn($record) => $record->subjectAssignment->is_active ? 'Aktivu' : 'Non-Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Edita'),
                Tables\Actions\DeleteAction::make()->label('Apaga')
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTimetables::route('/'),
            'create' => Pages\CreateTimetable::route('/create'),
            'edit' => Pages\EditTimetable::route('/{record}/edit'),
        ];
    }
}
