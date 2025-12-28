<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubjectAssignmentResource\Pages;
use App\Models\SubjectAssignment;
use App\Models\ClassRoom;
use App\Models\AcademicYear;
use App\Models\Period;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubjectAssignmentResource extends Resource
{
    protected static ?string $model = SubjectAssignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Alokasaun Disiplina';
    protected static ?string $navigationGroup = 'Managementu Professores';

    /* =====================================================
       DEFAULT QUERY → Academic Year & Period Aktif
    ===================================================== */
    public static function getEloquentQuery(): Builder
    {
        $activeYearId = AcademicYear::where('is_active', true)->value('id');
        $activePeriodId = Period::where('is_active', true)
            ->where('academic_year_id', $activeYearId)
            ->value('id');

        return parent::getEloquentQuery()
            ->with(['classRooms.major', 'teacher', 'subjects', 'academicYear', 'period'])
            ->where('academic_year_id', $activeYearId)
            ->where('period_id', $activePeriodId);
    }

    /* =====================================================
       FORM → CREATE / EDIT
    ===================================================== */
    public static function form(Form $form): Form
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $activePeriod = Period::where('is_active', true)
            ->where('academic_year_id', $activeYear?->id)
            ->first();

        return $form->schema([
            Forms\Components\Select::make('teacher_id')
                ->label('Naran Professor')
                ->relationship('teacher', 'name')
                ->required(),

            Forms\Components\Select::make('subjects')
                ->label('Disiplina')
                ->relationship('subjects', 'name')
                ->multiple()
                ->preload()
                ->required(),

            Forms\Components\Select::make('academic_year_id')
            ->label('Tinan Akademiku')
            ->options(AcademicYear::orderBy('name')->pluck('name', 'id'))
            ->default($activeYear?->id) // pakai $activeYear, bukan $activeYearId
            ->disabled(),

            Forms\Components\Select::make('period_id')
                ->label('Periodu')
                ->options(
                    Period::where('academic_year_id', $activeYear?->id)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                )
                ->default($activePeriod?->id) // pakai $activePeriod, bukan $activePeriodId
                ->disabled(),


            Forms\Components\Select::make('classRooms')
                ->label('Klasse / Turma')
                ->multiple()
                ->options(ClassRoom::with('major')->get()->mapWithKeys(fn($c) => [
                    $c->id => "{$c->level} {$c->turma} ({$c->major->name})"
                ]))
                ->required(),

            Forms\Components\Toggle::make('is_active')
                ->label('Aktivu')
                ->default(true),
        ]);
    }

    /* =====================================================
       TABLE
    ===================================================== */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('teacher.name')->label('Professor')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('subjects.name')->label('Disiplina')->badge()->separator(', '),

                // ClassRooms tampil
                Tables\Columns\TextColumn::make('classRooms')
                    ->label('Klasse / Turma')
                    ->getStateUsing(fn($record) => 
                        $record->classRooms->map(fn($c) =>
                            "{$c->level} {$c->turma} ({$c->major->name})"
                        )->join(', ')
                    )
                    ->wrap(),

                Tables\Columns\TextColumn::make('academicYear.name')->label('Tinan Akademiku'),
                Tables\Columns\TextColumn::make('period.name')->label('Periodu'),
                Tables\Columns\IconColumn::make('is_active')->label('Aktivu')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('classRooms')
                    ->label('Klasse / Turma')
                    ->options(ClassRoom::with('major')->get()->mapWithKeys(fn($c) => [
                        $c->id => "{$c->level} {$c->turma} ({$c->major->name})"
                    ]))
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('teacher_id')
                    ->label('Professor')
                    ->options(fn() => \App\Models\Teacher::orderBy('name')->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('subjects')
                    ->label('Disiplina')
                    ->options(fn() => \App\Models\Subject::orderBy('name')->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubjectAssignments::route('/'),
            'create' => Pages\CreateSubjectAssignment::route('/create'),
            'edit' => Pages\EditSubjectAssignment::route('/{record}/edit'),
        ];
    }
}
