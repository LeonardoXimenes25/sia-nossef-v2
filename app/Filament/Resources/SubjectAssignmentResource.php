<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubjectAssignmentResource\Pages;
use App\Models\SubjectAssignment;
use App\Models\ClassRoom;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubjectAssignmentResource extends Resource
{
    protected static ?string $model = SubjectAssignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Penugasan Guru';
    protected static ?string $navigationGroup = 'Managementu Akademiku';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Pilih Guru
                Forms\Components\Select::make('teacher_id')
                    ->relationship('teacher', 'name')
                    ->label('Guru')
                    ->required(),

                // Pilih Mata Pelajaran
                Forms\Components\Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->label('Mata Pelajaran')
                    ->required(),

                // Pilih Tahun Ajaran
                Forms\Components\Select::make('academic_year_id')
                    ->relationship('academicYear', 'name')
                    ->label('Tahun Ajaran')
                    ->required(),

                // Pilih Periode
                Forms\Components\Select::make('period_id')
                    ->relationship('period', 'name')
                    ->label('Periode')
                    ->required(),

                // Pilih Banyak Kelas (Many-to-Many)
                Forms\Components\Select::make('classRooms')
                    ->multiple()
                    ->label('Kelas / Turma')
                    ->options(function () {
                        return ClassRoom::with('major')->get()->mapWithKeys(function ($classRoom) {
                            return [
                                $classRoom->id => 
                                    $classRoom->level . ' ' . 
                                    $classRoom->turma . ' (' . 
                                    $classRoom->major->name . ')',
                            ];
                        });
                    })
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('Guru')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Mata Pelajaran')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('Tahun Ajaran')
                    ->sortable(),

                Tables\Columns\TextColumn::make('period.name')
                    ->label('Periode')
                    ->sortable(),

                // Kolom Kelas / Turma (tampilkan level, turma, dan jurusan)
                Tables\Columns\TextColumn::make('classRooms')
                    ->label('Kelas / Turma')
                    ->getStateUsing(fn($record) => 
                        $record->classRooms->map(fn($cr) => 
                            $cr->level . ' ' . $cr->turma . ' (' . $cr->major->name . ')'
                        )->join(', ')
                    )
                    ->wrap() // agar teks panjang bisa turun ke baris baru
                    ->limit(60),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()->label('Edita'),
                Tables\Actions\DeleteAction::make()->label('Apaga'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
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
