<?php

namespace App\Filament\Widgets;

use App\Models\Timetable;
use App\Models\AcademicYear;
use App\Models\Period;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class LatestTimetables extends BaseWidget
{
    protected static ?int $sort = 0; // ⬇️ DI BAWAH Stats
    protected static ?string $heading = "Horáriu";
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = Auth::user();

        $activeAcademicYear = AcademicYear::where('is_active', true)->first();
        $activePeriod = Period::where('is_active', true)->first();

        return $table
            ->query(
                Timetable::query()
                    ->with([
                        'subject',
                        'classRoom',
                        'academicYear',
                        'subjectAssignment.teacher',
                    ])
                    ->when($activeAcademicYear, fn ($q) =>
                        $q->where('academic_year_id', $activeAcademicYear->id)
                    )
                    ->when($activePeriod, fn ($q) =>
                        $q->where('period_id', $activePeriod->id)
                    )
                    ->when($user->hasRole('mestre') && $user->teacher, fn ($q) =>
                        $q->whereHas('subjectAssignment', fn ($sq) =>
                            $sq->where('teacher_id', $user->teacher->id)
                        )
                    )
                    ->when($user->hasRole('estudante') && $user->student, fn ($q) =>
                        $q->where('class_room_id', $user->student->class_room_id)
                    )
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Disiplina'),

                Tables\Columns\TextColumn::make('classRoom')
                    ->label('Klasse')
                    ->formatStateUsing(fn ($record) =>
                        $record->classRoom
                            ? $record->classRoom->level . ' ' . $record->classRoom->turma
                            : '-'
                    ),

                Tables\Columns\TextColumn::make('academicYear.year')
                    ->label('Ano Akademiku')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('day')
                    ->label('Dia'),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Hora Inisiu')
                    ->time('H:i'),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('Hora Remata')
                    ->time('H:i'),
            ])
            ->paginated(false)
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }
}
