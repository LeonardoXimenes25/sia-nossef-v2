<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GradeResource\Pages;
use App\Models\ClassRoom;
use App\Models\Grade;
use App\Models\Student;
use App\Models\SubjectAssignment;
use Filament\Forms;
use Filament\Forms\Form;
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

    // Method untuk generate remarks berdasarkan score
    public static function getRemarksByScore($score): string
    {
        return match(true) {
            $score >= 9.5 => 'Excelente',
            $score >= 8.5 => 'Muito Bom',
            $score >= 7.0 => 'Bom',
            $score >= 6.0 => 'Suficiente',
            $score >= 5.0 => 'Insuficiente',
            $score >= 3.0 => 'Mau',
            $score >= 1.0 => 'Muito Mau',
            default => 'Não Avaliado'
        };
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Pilih Kelas / Turma
                Forms\Components\Select::make('class_room_id')
                    ->label('Klasse / Turma')
                    ->options(function () {
                        return ClassRoom::with('major')->get()->mapWithKeys(function ($classRoom) {
                            return [
                                $classRoom->id => $classRoom->level . ' ' . $classRoom->turma . ' (' . $classRoom->major->name . ')',
                            ];
                        });
                    })
                    ->live()
                    ->required()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $students = Student::where('class_room_id', $state)
                            ->orderBy('name')
                            ->get(['id', 'name', 'nre'])
                            ->map(function ($student) {
                                return [
                                    'id' => $student->id,
                                    'nre' => $student->nre,
                                    'name' => $student->name,
                                    'score' => 0,
                                    'remarks' => 'Não Avaliado',
                                ];
                            })
                            ->toArray();

                        $set('students', $students);
                    }),

                // Pilih Subject Assignment (Guru + Mata Pelajaran)
                Forms\Components\Select::make('subject_assignment_id')
                    ->label('Professor / Materia')
                    ->options(function () {
                        return SubjectAssignment::with(['teacher', 'subject'])
                            ->get()
                            ->mapWithKeys(fn($assignment) => [
                                $assignment->id => $assignment->teacher->name . ' - ' . $assignment->subject->name,
                            ]);
                    })
                    ->searchable()
                    ->required(),

                // Tahun Akademik
                Forms\Components\Select::make('academic_year_id')
                    ->label('Tinan Akademiku')
                    ->relationship('academicYear', 'name')
                    ->required(),

                // Periode
                Forms\Components\Select::make('period_id')
                    ->label('Periodu')
                    ->relationship('period', 'name')
                    ->required(),

                // Repeater untuk students dengan nilai
                Forms\Components\Repeater::make('students')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                // NRE
                                Forms\Components\TextInput::make('nre')
                                    ->label('NRE')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->columnSpan(1),
                                
                                // Nama Siswa
                                Forms\Components\TextInput::make('name')
                                    ->label('Naran Estudante')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->columnSpan(2),
                                
                                // Nilai
                                Forms\Components\TextInput::make('score')
                                    ->label('Valor')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(10)
                                    ->step(0.1)
                                    ->required()
                                    ->columnSpan(1)
                                    ->suffix('/10')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, $context) {
                                        // Auto-generate remarks berdasarkan score
                                        if ($state !== null && $state !== '') {
                                            $remarks = self::getRemarksByScore((float)$state);
                                            $set('remarks', $remarks);
                                        }
                                    }),
                                
                                // Remarks - Auto-generated
                                Forms\Components\TextInput::make('remarks')
                                    ->label('Observasaun')
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(2)
                                    ->default('Não Avaliado'),
                                
                                // Hidden ID
                                Forms\Components\Hidden::make('id')
                                    ->required(),
                            ])
                            ->columns(6)
                    ])
                    ->defaultItems(0)
                    ->reorderable(false)
                    ->deletable(false)
                    ->addable(false)
                    ->itemLabel(fn (array $state): ?string => 
                        ($state['nre'] ?? '') . ' - ' . ($state['name'] ?? 'Naran la iha')
                    )
                    ->visible(fn (Forms\Get $get) => !empty($get('class_room_id')))
                    ->label('Lista Estudante sira')
                    ->columnSpan('full')
                    ->extraAttributes(['class' => 'bg-gray-50 p-6 rounded-lg border border-gray-200']),
            ]);
    }

     public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.nre')
                    ->label('NRE')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Estudante')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('classRoom.level')
                    ->label('Kelas')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('classRoom.turma')
                    ->label('Turma')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('subjectAssignment.subject.name')
                    ->label('Materia')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('subjectAssignment.teacher.name')
                    ->label('Professor')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('score')
                    ->label('Valor')
                    ->sortable()
                    ->formatStateUsing(fn($state) => number_format($state, 1))
                    ->color(fn($state) => match(true) {
                        $state >= 8.5 => 'success',
                        $state >= 7.0 => 'warning',
                        $state >= 6.0 => 'info',
                        default => 'danger',
                    })
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('remarks')
                    ->label('Observasaun')
                    ->color(fn($state) => match($state) {
                        'Excelente', 'Muito Bom' => 'success',
                        'Bom' => 'warning',
                        'Suficiente' => 'info',
                        'Insuficiente', 'Mau', 'Muito Mau' => 'danger',
                        default => 'gray',
                    })
                    ->badge()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('Tinan Akademiku')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('period.name')
                    ->label('Periodu')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data Kria')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                // Filter berdasarkan Kelas
                Tables\Filters\SelectFilter::make('class_room_id')
                    ->label('Klasse / Turma')
                    ->options(
                        ClassRoom::with(['major'])
                            ->get()
                            ->mapWithKeys(fn($class) => [
                                $class->id => $class->level . ' ' . $class->turma . ' (' . $class->major->name . ')'
                            ])
                    )
                    ->searchable()
                    ->multiple()
                    ->preload(),

                // Filter berdasarkan Mata Pelajaran
                Tables\Filters\SelectFilter::make('subject_assignment_id')
                    ->label('Materia')
                    ->options(
                        SubjectAssignment::with(['subject'])
                            ->get()
                            ->mapWithKeys(fn($assignment) => [
                                $assignment->id => $assignment->subject->name
                            ])
                    )
                    ->searchable()
                    ->multiple()
                    ->preload(),

                // Filter berdasarkan Guru
                Tables\Filters\SelectFilter::make('teacher')
                    ->label('Professor')
                    ->relationship('subjectAssignment.teacher', 'name')
                    ->searchable()
                    ->multiple()
                    ->preload(),

                // Filter berdasarkan Tahun Akademik
                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('Tinan Akademiku')
                    ->relationship('academicYear', 'name')
                    ->searchable()
                    ->preload(),

                // Filter berdasarkan Periode
                Tables\Filters\SelectFilter::make('period_id')
                    ->label('Periodu')
                    ->relationship('period', 'name')
                    ->searchable()
                    ->preload(),

                // Filter berdasarkan Remarks
                Tables\Filters\SelectFilter::make('remarks')
                    ->label('Observasaun')
                    ->options([
                        'Excelente' => 'Excelente',
                        'Muito Bom' => 'Muito Bom',
                        'Bom' => 'Bom',
                        'Suficiente' => 'Suficiente',
                        'Insuficiente' => 'Insuficiente',
                        'Mau' => 'Mau',
                        'Muito Mau' => 'Muito Mau',
                        'Não Avaliado' => 'Não Avaliado',
                    ])
                    ->searchable()
                    ->preload(),

                // Filter berdasarkan Rentang Nilai
                Tables\Filters\Filter::make('score_range')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('min_score')
                                    ->label('Valor Minimu')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(10)
                                    ->placeholder('0'),
                                Forms\Components\TextInput::make('max_score')
                                    ->label('Valor Máximu')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(10)
                                    ->placeholder('10'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_score'] ?? null,
                                fn (Builder $query, $score): Builder => $query->where('score', '>=', $score),
                            )
                            ->when(
                                $data['max_score'] ?? null,
                                fn (Builder $query, $score): Builder => $query->where('score', '<=', $score),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['min_score'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Min: ' . $data['min_score']);
                        }
                        if ($data['max_score'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Max: ' . $data['max_score']);
                        }
                        return $indicators;
                    }),

                // Quick Filter - Nilai Baik
                Tables\Filters\Filter::make('good_scores')
                    ->label('Valor Diak')
                    ->query(fn(Builder $query): Builder => $query->where('score', '>=', 7.0))
                    ->toggle()
                    ->default(false),

                // Quick Filter - Nilai Cukup
                Tables\Filters\Filter::make('average_scores')
                    ->label('Valor Mediu')
                    ->query(fn(Builder $query): Builder => $query->whereBetween('score', [5.0, 6.9]))
                    ->toggle()
                    ->default(false),

                // Quick Filter - Nilai Rendah
                Tables\Filters\Filter::make('poor_scores')
                    ->label('Valor Kraik')
                    ->query(fn(Builder $query): Builder => $query->where('score', '<', 5.0))
                    ->toggle()
                    ->default(false),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->filtersFormColumns(4) // Tambah jadi 4 kolom untuk lebih compact
            ->headerActions([
                // Search Global - DIPINDAH KE SINI
                Tables\Actions\Action::make('search')
                    ->hidden(), // Sembunyikan, karena kita akan gunakan built-in search
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Edita'),
                Tables\Actions\DeleteAction::make()->label('Apaga'),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->fileName('Valor_' . date('Y-m-d_H-i-s'))
                    ->defaultFormat('pdf')
                    ->color('success')
                    ->label('Exporta PDF'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListGrades::route('/'),
            'create' => Pages\CreateGrade::route('/create'),
            'edit' => Pages\EditGrade::route('/{record}/edit'),
        ];
    }
}