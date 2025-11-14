<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Timetable;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\SubjectAssignment;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TimetableResource\Pages;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;

class TimetableResource extends Resource
{
    protected static ?string $model = Timetable::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Horariu';
    protected static ?string $navigationGroup = 'Managementu Akademiku';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Pilih SubjectAssignment (Guru + Mata Pelajaran)
                Forms\Components\Select::make('subject_assignment_id')
                    ->label('Profesor / Materia')
                    ->options(function() {
                        return SubjectAssignment::with(['teacher','subject'])->get()
                            ->mapWithKeys(fn($sa) => [$sa->id => $sa->teacher->name . ' - ' . $sa->subject->name]);
                    })
                    ->reactive()
                    ->required(),

                // Pilih ClassRoom sesuai SubjectAssignment yang dipilih
                Forms\Components\Select::make('class_room_id')
                    ->label('Clase / Turma')
                    ->options(function ($get) {
                        $assignment = SubjectAssignment::with('classRooms.major')->find($get('subject_assignment_id'));
                        if (!$assignment) return [];
                        return $assignment->classRooms->mapWithKeys(function($cr) {
                            return [
                                $cr->id => $cr->level . ' ' . $cr->turma . ' (' . $cr->major->name . ')'
                            ];
                        });
                    })
                    ->required(),

                Forms\Components\Select::make('day')
                    ->label('Hari')
                    ->options([
                        'Monday' => 'Segunda',
                        'Tuesday' => 'Tersa',
                        'Wednesday' => 'Kuarta',
                        'Thursday' => 'Kinta',
                        'Friday' => 'Sexta',
                        'Saturday' => 'Sabadu',
                    ])
                    ->required(),

                Forms\Components\TimePicker::make('start_time')->label('Oras Hahu')->required(),
                Forms\Components\TimePicker::make('end_time')->label('Oras Ramata')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subjectAssignment.teacher.name')->label('Profesor'),
                Tables\Columns\TextColumn::make('subjectAssignment.subject.name')->label('Materia'),
                Tables\Columns\TextColumn::make('classRoom.level')->label('Klase'),
                Tables\Columns\TextColumn::make('classRoom.turma')->label('Turma'),
                Tables\Columns\TextColumn::make('classRoom.major.name')->label('Area Estudu'),
                Tables\Columns\TextColumn::make('day')->label('Loron'),
                Tables\Columns\TextColumn::make('start_time')->label('Oras Hahu'),
                Tables\Columns\TextColumn::make('end_time')->label('Oras Ramata'),
            ])
            ->filters([
                
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->fileName('Timetables')
                    ->defaultFormat('pdf')
                    ->color('success')
                    ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Edit'),
                Tables\Actions\DeleteAction::make()->label('Hapus'),
            ])
            ->bulkActions([
            ]);
    }

    // Eager load supaya relasi nested muncul di tabel
    public static function getTableQuery(): Builder
    {
        return parent::getTableQuery()->with([
            'subjectAssignment.teacher',
            'subjectAssignment.subject',
            'classRoom.major',
        ]);
    }

    public static function getRelations(): array
    {
        return [];
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
