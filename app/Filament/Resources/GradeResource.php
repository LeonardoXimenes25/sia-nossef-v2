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

    // Generate remarks berdasarkan score
    public static function getRemarksByScore($score): string
    {
        if ($score === null || $score === '') {
            return 'Não Avaliado';
        }
        
        $score = (float) $score;
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
                Select::make('class_room_id')
                    ->label('Klasse / Turma')
                    ->options(function () {
                        return ClassRoom::with('major')->get()->mapWithKeys(fn($c)=>[
                            $c->id => $c->level.' '.$c->turma.' ('.$c->major->name.')'
                        ]);
                    })
                    ->live()
                    ->required()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if (!$state) {
                            $set('students', []);
                            return;
                        }
                        
                        $students = Student::where('class_room_id', $state)
                            ->orderBy('name')
                            ->get(['id','name','nre'])
                            ->map(fn($s)=>[
                                'id' => $s->id,
                                'nre' => $s->nre,
                                'name' => $s->name,
                                'score' => '',
                                'remarks' => 'Não Avaliado',
                            ])
                            ->toArray();
                        $set('students', $students);
                    }),

                // Pilih Subject Assignment (Guru + Mata Pelajaran)
                Select::make('subject_assignment_id')
                    ->label('Professor / Materia')
                    ->options(function () {
                        return SubjectAssignment::with(['teacher','subject'])
                            ->get()
                            ->mapWithKeys(fn($s)=>[$s->id=>$s->teacher->name.' - '.$s->subject->name]);
                    })
                    ->searchable()
                    ->required(),

                // Tahun Akademik
                Select::make('academic_year_id')
                    ->label('Tinan Akademiku')
                    ->relationship('academicYear','name')
                    ->required(),

                // Periode
                Select::make('period_id')
                    ->label('Periodu')
                    ->relationship('period','name')
                    ->required(),

                // Tabel siswa dengan tampilan spreadsheet
                Forms\Components\Section::make('Tabel Estudante sira')
                    ->schema([
                        // Header tabel menggunakan Placeholder dengan HTML
                        Forms\Components\Placeholder::make('table_header')
                            ->content(view('filament.components.grade-table-header'))
                            ->extraAttributes(['class' => 'p-0']),
                        
                        // Atau alternatif: Header menggunakan Grid dengan TextInput yang disabled
                        Grid::make(5)
                            ->schema([
                                TextInput::make('header_nre')
                                    ->label('NRE')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->extraAttributes(['class' => 'font-bold bg-gray-100 border-0'])
                                    ->default('NRE'),
                                TextInput::make('header_name')
                                    ->label('Naran Estudante')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->extraAttributes(['class' => 'font-bold bg-gray-100 border-0'])
                                    ->default('Naran Estudante'),
                                TextInput::make('header_score')
                                    ->label('Valor')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->extraAttributes(['class' => 'font-bold bg-gray-100 border-0'])
                                    ->default('Valor'),
                                TextInput::make('header_remarks')
                                    ->label('Observasaun')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->extraAttributes(['class' => 'font-bold bg-gray-100 border-0'])
                                    ->default('Observasaun'),
                                TextInput::make('header_id')
                                    ->label('ID')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->extraAttributes(['class' => 'font-bold bg-gray-100 border-0'])
                                    ->default('ID'),
                            ])
                            ->columns(5)
                            ->extraAttributes(['class' => 'border-b-2 border-gray-300 pb-2 mb-2']),

                        // Data siswa dalam repeater
                        Forms\Components\Repeater::make('students')
                            ->schema([
                                Grid::make(5)
                                    ->schema([
                                        TextInput::make('nre')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->extraAttributes(['class' => 'bg-gray-50']),
                                        TextInput::make('name')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->extraAttributes(['class' => 'bg-gray-50']),
                                        TextInput::make('score')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(10)
                                            ->step(0.1)
                                            ->placeholder('0-10')
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, Forms\Set $set, $get) {
                                                $remarks = GradeResource::getRemarksByScore($state);
                                                $set('remarks', $remarks);
                                            }),
                                        TextInput::make('remarks')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->extraAttributes(['class' => 'bg-gray-50']),
                                        Hidden::make('id')
                                            ->required(),
                                    ])
                                    ->columns(5)
                                    ->extraAttributes(['class' => 'border-b border-gray-200 py-2'])
                            ])
                            ->defaultItems(0)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->label(false)
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->grid(1)
                            ->columnSpanFull()
                    ])
                    ->collapsible()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.nre')->label('NRE')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('student.name')->label('Estudante')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('classRoom.level')->label('Kelas')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('classRoom.turma')->label('Turma')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('subjectAssignment.subject.name')->label('Materia')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('subjectAssignment.teacher.name')->label('Professor')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('score')
                    ->label('Valor')
                    ->sortable()
                    ->formatStateUsing(fn($s)=> $s ? number_format($s, 1) : '-')
                    ->color(fn($s)=> match(true){
                        $s >= 8.5 => 'success',
                        $s >= 7.0 => 'warning',
                        $s >= 6.0 => 'info',
                        $s > 0 => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('remarks')
                    ->label('Observasaun')
                    ->color(fn($r)=> match($r){
                        'Excelente','Muito Bom'=>'success',
                        'Bom'=>'warning',
                        'Suficiente'=>'info',
                        'Insuficiente','Mau','Muito Mau'=>'danger',
                        default=>'gray',
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('academicYear.name')->label('Tinan Akademiku')->sortable(),
                Tables\Columns\TextColumn::make('period.name')->label('Periodu')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Data Kria')->date('d M Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('class_room_id')
                    ->label('Klasse / Turma')
                    ->options(ClassRoom::with('major')->get()->mapWithKeys(fn($c)=>[$c->id=>$c->level.' '.$c->turma.' ('.$c->major->name.')']))
                    ->searchable()
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('subject_assignment_id')
                    ->label('Materia')
                    ->options(SubjectAssignment::with('subject')->get()->mapWithKeys(fn($s)=>[$s->id=>$s->subject->name]))
                    ->searchable()
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('teacher')
                    ->label('Professor')
                    ->relationship('subjectAssignment.teacher','name')
                    ->searchable()
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('Tinan Akademiku')
                    ->relationship('academicYear','name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('period_id')
                    ->label('Periodu')
                    ->relationship('period','name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('remarks')
                    ->label('Observasaun')
                    ->options([
                        'Excelente'=>'Excelente','Muito Bom'=>'Muito Bom','Bom'=>'Bom','Suficiente'=>'Suficiente',
                        'Insuficiente'=>'Insuficiente','Mau'=>'Mau','Muito Mau'=>'Muito Mau','Não Avaliado'=>'Não Avaliado'
                    ])
                    ->searchable()
                    ->preload(),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->fileName('Valor_'.date('Y-m-d_H-i-s'))
                    ->defaultFormat('pdf')
                    ->color('success')
                    ->label('Exporta PDF'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Edita'),
                Tables\Actions\DeleteAction::make()->label('Apaga'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at','desc');
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