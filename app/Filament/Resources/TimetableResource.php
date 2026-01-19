<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Period;
use App\Models\Timetable;
use App\Models\AcademicYear;
use Filament\Resources\Resource;
use App\Models\SubjectAssignment;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TimetableResource\Pages;
use Illuminate\Support\Facades\Auth;

class TimetableResource extends Resource
{
    protected static ?string $model = Timetable::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Horariu';

    protected static array $dayLabels = [
        'Monday'    => 'Segunda',
        'Tuesday'   => 'Tersa',
        'Wednesday' => 'Kuarta',
        'Thursday'  => 'Kinta',
        'Friday'    => 'Sexta',
        'Saturday'  => 'Sabadu',
    ];

    /**
     * 🔒 FILTER DATA BERDASARKAN ROLE
     */
    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        $activeYear = AcademicYear::where('is_active', true)->first();
        $activePeriod = Period::where('is_active', true)
            ->where('academic_year_id', $activeYear?->id)
            ->first();

        $query = parent::getEloquentQuery()
            ->with([
                'subjectAssignment.teacher',
                'subject',
                'classRoom.major',
                'academicYear',
                'period',
            ])
            ->where('academic_year_id', $activeYear?->id)
            ->where('period_id', $activePeriod?->id);

        // 🔐 KHUSUS ESTUDANTE
        if ($user && $user->hasRole('estudante')) {

            $classRoomId = $user->student?->class_room_id;

            if ($classRoomId) {
                $query->where('class_room_id', $classRoomId);
            } else {
                // kalau estudante belum punya kelas → kosong
                $query->whereRaw('1 = 0');
            }
        }

        return $query;
    }

    /**
     * 📝 FORM
     */
    public static function form(Forms\Form $form): Forms\Form
    {
        $activeYearId = AcademicYear::where('is_active', true)->value('id');
        $activePeriodId = Period::where('is_active', true)
            ->where('academic_year_id', $activeYearId)
            ->value('id');

        $user = Auth::user();
        $isTeacher = $user && $user->teacher;
        $teacherId = $isTeacher ? $user->teacher->id : null;

        return $form->schema([
            Select::make('academic_year_id')
                ->label('Tinan Akademiku')
                ->options(AcademicYear::pluck('name', 'id'))
                ->default($activeYearId)
                ->disabled()
                ->required(),

            Select::make('period_id')
                ->label('Periodu')
                ->options(
                    Period::where('academic_year_id', $activeYearId)
                        ->pluck('name', 'id')
                )
                ->default($activePeriodId)
                ->disabled()
                ->required(),

            Select::make('subject_assignment_id')
                ->label('Profesor')
                ->options(fn () =>
                    $isTeacher
                        ? SubjectAssignment::where('teacher_id', $teacherId)
                            ->where('is_active', true)
                            ->with('teacher')
                            ->get()
                            ->pluck('teacher.name', 'id')
                        : SubjectAssignment::where('is_active', true)
                            ->with('teacher')
                            ->get()
                            ->pluck('teacher.name', 'id')
                )
                ->disabled($isTeacher)
                ->searchable()
                ->preload()
                ->live()
                ->required(),

            Select::make('subject_id')
                ->label('Disiplina')
                ->options(function (callable $get, $record) {
                    if ($record) {
                        return [$record->subject_id => $record->subject->name];
                    }

                    $saId = $get('subject_assignment_id');
                    if (!$saId) return [];

                    return SubjectAssignment::find($saId)
                        ?->subjects
                        ->pluck('name', 'id')
                        ->toArray() ?? [];
                })
                ->required()
                ->reactive(),

            Select::make('class_room_id')
                ->label('Klasse / Turma')
                ->options(function (callable $get, $record) {
                    if ($record) {
                        $cr = $record->classRoom;
                        return [
                            $cr->id => "{$cr->level} {$cr->turma} ({$cr->major?->name})"
                        ];
                    }

                    $saId = $get('subject_assignment_id');
                    if (!$saId) return [];

                    return SubjectAssignment::with('classRooms.major')
                        ->find($saId)
                        ?->classRooms
                        ->mapWithKeys(fn ($cr) => [
                            $cr->id => "{$cr->level} {$cr->turma} ({$cr->major?->name})"
                        ])
                        ->toArray() ?? [];
                })
                ->searchable()
                ->required(),

            Select::make('day')
                ->label('Loron')
                ->options(self::$dayLabels)
                ->required(),

            TimePicker::make('start_time')->label('Oras Hahu')->required(),
            TimePicker::make('end_time')->label('Oras Ramata')->required(),
        ]);
    }

    /**
     * 📊 TABLE
     */
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('Nu')->sortable(),
                TextColumn::make('subjectAssignment.teacher.name')->label('Profesor'),
                TextColumn::make('subject.name')->label('Disiplina'),
                TextColumn::make('classRoom.level')->label('Klase'),
                TextColumn::make('classRoom.turma')->label('Turma'),
                TextColumn::make('classRoom.major.name')->label('Area Estudu'),
                TextColumn::make('day')
                    ->label('Loron')
                    ->formatStateUsing(fn ($state) => self::$dayLabels[$state] ?? $state),
                TextColumn::make('start_time')->label('Oras Hahu'),
                TextColumn::make('end_time')->label('Oras Ramata'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => !Auth::user()?->hasRole('estudante')),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => !Auth::user()?->hasRole('estudante')),
            ])
            ->defaultSort('id', 'desc');
    }

    /**
     * 🚫 DISABLE CREATE UNTUK ESTUDANTE
     */
    public static function canCreate(): bool
    {
        return !Auth::user()?->hasRole('estudante');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTimetables::route('/'),
            'create' => Pages\CreateTimetable::route('/create'),
            'edit'   => Pages\EditTimetable::route('/{record}/edit'),
        ];
    }
}
