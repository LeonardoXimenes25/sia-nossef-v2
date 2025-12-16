<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\Attendance;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AttendanceResource\Pages;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Absensi';
    protected static ?string $pluralModelLabel = 'Absensi';

    /* =========================
     * FORM
     * ========================= */
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('class_room_id')
                ->label('Klasse / Turma')
                ->options(
                    ClassRoom::with('major')->get()->mapWithKeys(fn ($class) => [
                        $class->id =>
                            $class->level . ' ' .
                            $class->turma . ' (' .
                            $class->major->name . ')',
                    ])
                )
                ->live()
                ->required()
                ->afterStateUpdated(function ($state, Forms\Set $set) {
                    $students = Student::where('class_room_id', $state)
                        ->orderBy('name')
                        ->get(['id', 'name', 'nre'])
                        ->map(fn ($s) => [
                            'id' => $s->id,
                            'nre' => $s->nre,
                            'name' => $s->name,
                            'status' => 'presente',
                        ])
                        ->toArray();

                    $set('students', $students);
                }),

            Forms\Components\DatePicker::make('date')
                ->label('Data')
                ->default(now())
                ->required(),

            Forms\Components\Repeater::make('students')
                ->schema([
                    Forms\Components\TextInput::make('nre')
                        ->disabled()
                        ->dehydrated(false),

                    Forms\Components\TextInput::make('name')
                        ->disabled()
                        ->dehydrated(false),

                    Forms\Components\Select::make('status')
                        ->options([
                            'presente' => 'Presente',
                            'moras' => 'Moras',
                            'lisensa' => 'Lisensa',
                            'falta' => 'Falta',
                        ])
                        ->required(),

                    Forms\Components\Hidden::make('id')->required(),
                ])
                ->columns(4)
                ->addable(false)
                ->deletable(false)
                ->reorderable(false)
                ->visible(fn (Forms\Get $get) => filled($get('class_room_id')))
                ->columnSpanFull(),
        ]);
    }

    /* =========================
     * TABLE
     * ========================= */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.nre')
                    ->label('NRE')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('student.name')
                    ->label('Naran Estudante')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('classRoom.level')
                    ->label('Klasse'),

                Tables\Columns\TextColumn::make('classRoom.turma')
                    ->label('Turma'),

                Tables\Columns\TextColumn::make('classRoom.major.code')
                    ->label('Area Estudu'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'presente',
                        'warning' => 'moras',
                        'info' => 'lisensa',
                        'danger' => 'falta',
                    ]),

                Tables\Columns\TextColumn::make('date')
                    ->label('Data')
                    ->date('d M Y')
                    ->sortable(),
            ])

            ->filters([
                // 🔹 Status
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'presente' => 'Presente',
                        'moras' => 'Moras',
                        'lisensa' => 'Lisensa',
                        'falta' => 'Falta',
                    ])
                    ->multiple(),

                // 🔹 Kelas (default dari session)
                Tables\Filters\SelectFilter::make('class_room_id')
                    ->label('Kelas')
                    ->options(
                        ClassRoom::with('major')->get()->mapWithKeys(fn ($class) => [
                            $class->id =>
                                $class->level . ' ' .
                                $class->turma . ' (' .
                                $class->major->name . ')',
                        ])
                    )
                    ->default(fn () => session('attendance_class_room_id'))
                    ->searchable()
                    ->preload(),

                // 🔥 FILTER TANGGAL (DEFAULT = HARI INI)
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('date')
                            ->label('Tanggal')
                            ->default(now())
                            ->required(),
                    ])
                    ->query(fn (Builder $query, array $data) =>
                        $query->whereDate('date', $data['date'])
                    ),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
