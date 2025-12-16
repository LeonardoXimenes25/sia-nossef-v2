<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GradeResource\Pages;
use App\Models\ClassRoom;
use App\Models\Grade;
use App\Models\Student;
use App\Models\SubjectAssignment;
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
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;

class GradeResource extends Resource
{
    protected static ?string $model = Grade::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Valor';
    protected static ?string $pluralModelLabel = 'Valor';

    /* ===============================
     |  REMARKS BY SCORE
     =============================== */
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

    /* ===============================
     |  FORM CREATE/EDIT
     =============================== */
    public static function form(Form $form): Form
    {
        return $form->schema([
            /* === CLASS ROOM === */
            Select::make('class_room_id')
                ->label('Klasse / Turma')
                ->options(
                    ClassRoom::with('major')->get()->mapWithKeys(fn($c) => [
                        $c->id => $c->level . ' ' . $c->turma . ' (' . $c->major->name . ')',
                    ])
                )
                ->live()
                ->required()
                ->afterStateUpdated(function ($state, Forms\Set $set) {
                    if (!$state) {
                        $set('students', []);
                        return;
                    }

                    $students = Student::where('class_room_id', $state)
                        ->orderBy('name')
                        ->get(['id', 'name', 'nre'])
                        ->map(fn($s) => [
                            'id'      => $s->id,
                            'nre'     => $s->nre,
                            'name'    => $s->name,
                            'score'   => null,
                            'remarks' => 'Não Avaliado',
                        ])
                        ->toArray();

                    $set('students', $students);
                }),

            /* === SUBJECT ASSIGNMENT === */
            Select::make('subject_assignment_id')
                ->label('Professor / Materia')
                ->options(
                    SubjectAssignment::with(['teacher', 'subject'])
                        ->get()
                        ->mapWithKeys(fn($s) => [
                            $s->id => $s->teacher->name . ' - ' . $s->subject->name,
                        ])
                )
                ->searchable()
                ->required(),

            /* === ACADEMIC YEAR === */
            Select::make('academic_year_id')
                ->label('Tinan Akademiku')
                ->relationship('academicYear', 'name')
                ->required(),

            /* === PERIOD === */
            Select::make('period_id')
                ->label('Periodu')
                ->relationship('period', 'name')
                ->required(),

            /* === STUDENT TABLE === */
            Section::make('Tabel Estudante sira')
                ->schema([
                    Forms\Components\Repeater::make('students')
                        ->schema([
                            Grid::make(5)->schema([
                                TextInput::make('nre')->disabled()->dehydrated(false),
                                TextInput::make('name')->disabled()->dehydrated(false),

                                TextInput::make('score')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(10)
                                    ->step(0.1)
                                    ->reactive()
                                    ->afterStateUpdated(fn($state, Forms\Set $set) =>
                                        $set('remarks', self::getRemarksByScore($state))
                                    ),

                                TextInput::make('remarks')->disabled()->dehydrated(false),

                                Hidden::make('id')->required(),
                            ]),
                        ])
                        ->addable(false)
                        ->deletable(false)
                        ->reorderable(false)
                        ->label(false)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    /* ===============================
     |  TABLE INDEX
     =============================== */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.nre')->label('NRE')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('student.name')->label('Estudante')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('classRoom.level')->label('Kelas')->sortable(),
                Tables\Columns\TextColumn::make('classRoom.turma')->label('Turma')->sortable(),
                Tables\Columns\TextColumn::make('subjectAssignment.subject.name')->label('Materia'),
                Tables\Columns\TextColumn::make('subjectAssignment.teacher.name')->label('Professor'),

                Tables\Columns\TextColumn::make('score')
                    ->label('Valor')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state !== null ? number_format($state, 1) : '-')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 8.5 => 'success',
                        $state >= 7.0 => 'warning',
                        $state >= 6.0 => 'info',
                        $state > 0   => 'danger',
                        default      => 'gray',
                    }),

                Tables\Columns\TextColumn::make('remarks')
                    ->label('Observasaun')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Excelente', 'Muito Bom' => 'success',
                        'Bom'                    => 'warning',
                        'Suficiente'             => 'info',
                        'Insuficiente',
                        'Mau',
                        'Muito Mau'              => 'danger',
                        default                   => 'gray',
                    }),

                Tables\Columns\TextColumn::make('academicYear.name')->label('Tinan Akademiku'),
                Tables\Columns\TextColumn::make('period.name')->label('Periodu'),
                Tables\Columns\TextColumn::make('created_at')->label('Data Kria')->date('d M Y'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('class_room_id')
                    ->label('Kelas')
                    ->options(ClassRoom::with('major')->get()->mapWithKeys(fn($c) => [
                        $c->id => $c->level . ' ' . $c->turma . ' (' . $c->major->name . ')'
                    ])->toArray())
                    ->searchable()
                    ->preload()
                    ->default(fn () => ClassRoom::first()?->id),

                Tables\Filters\SelectFilter::make('subject_assignment_id')
                    ->label('Materia / Professor')
                    ->options(
                        SubjectAssignment::with(['teacher','subject'])
                            ->get()
                            ->mapWithKeys(fn($s) => [
                                $s->id => $s->teacher->name . ' - ' . $s->subject->name
                            ])->toArray()
                    )
                    ->searchable()
                    ->preload()
                    ->default(fn () => SubjectAssignment::first()?->id),

                Tables\Filters\SelectFilter::make('period_id')
                    ->relationship('period', 'name')
                    ->default(fn () => \App\Models\Period::latest()->first()?->id)
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->relationship('academicYear', 'name')
                    ->default(fn () => \App\Models\AcademicYear::latest()->first()?->id)
                    ->searchable()
                    ->preload(),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make()->label('Edita'),
                Tables\Actions\DeleteAction::make()->label('Apaga'),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->fileName('Grades_' . now()->format('Y-m-d_H-i-s'))
                    ->label('Exporta PDF')
                    ->color('success'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListGrades::route('/'),
            'create' => Pages\CreateGrade::route('/create'),
            'edit'   => Pages\EditGrade::route('/{record}/edit'),
        ];
    }
}
