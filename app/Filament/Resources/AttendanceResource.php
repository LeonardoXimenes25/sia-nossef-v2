<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Student;
use Filament\Forms\Form;
use App\Models\ClassRoom;
use App\Models\Attendance;
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Pilih kelas + turma
                Forms\Components\Select::make('class_room_id')
                    ->label('Klasse / Turma')
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
                                    'status' => 'presente',
                                ];
                            })
                            ->toArray();

                        $set('students', $students);
                    }),

                // Pilih tanggal absensi
                Forms\Components\DatePicker::make('date')
                    ->label('Data')
                    ->default(now())
                    ->required(),

                // Repeater untuk students dengan NRE
                Forms\Components\Repeater::make('students')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                // NRE
                                Forms\Components\TextInput::make('nre')
                                    ->label('NRE')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->columnSpan(1)
                                    ->maxLength(20),
                                
                                // Nama Siswa
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Siswa')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->columnSpan(2),
                                
                                // Status
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'presente' => 'Presente',
                                        'moras' => 'Moras', 
                                        'lisensa' => 'Lisensa',
                                        'falta' => 'Falta',
                                    ])
                                    ->default('presente')
                                    ->required()
                                    ->native(false)
                                    ->columnSpan(1),
                                
                                // Hidden ID
                                Forms\Components\Hidden::make('id')
                                    ->required(),
                            ])
                            ->columns(4)
                    ])
                    ->defaultItems(0)
                    ->reorderable(false)
                    ->deletable(false)
                    ->addable(false)
                    ->itemLabel(fn (array $state): ?string => 
                        ($state['nre'] ?? '') . ' - ' . ($state['name'] ?? 'Nama tidak tersedia')
                    )
                    ->visible(fn (Forms\Get $get) => !empty($get('class_room_id')))
                    ->label('Daftar Siswa')
                    ->columnSpan('full')
                    ->extraAttributes(['class' => 'bg-gray-50 p-6 rounded-lg border border-gray-200']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom NRE
                Tables\Columns\TextColumn::make('student.nre')
                    ->label('NRE')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                
                // Kolom Nama Siswa
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                
                // Kolom Kelas (Level)
                Tables\Columns\TextColumn::make('classRoom.level')
                    ->label('Kelas')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                
                // Kolom Turma
                Tables\Columns\TextColumn::make('classRoom.turma')
                    ->label('Turma')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                
                // Kolom Jurusan
                Tables\Columns\TextColumn::make('classRoom.major.name')
                    ->label('Jurusan')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                
                // Kolom Status
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->badge()
                    ->colors([
                        'success' => 'presente',
                        'warning' => 'moras',
                        'info' => 'lisensa',
                        'danger' => 'falta',
                    ])
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                
                // Kolom Tanggal
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                // Filter Status - SIMPLE & CLEAN
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'presente' => 'Presente',
                        'moras' => 'Moras',
                        'lisensa' => 'Lisensa',
                        'falta' => 'Falta',
                    ])
                    ->label('Status')
                    ->multiple()
                    ->preload(),

                // Filter Kelas & Turma - KOMBINASI
                Tables\Filters\SelectFilter::make('class_room_id')
                    ->label('Kelas')
                    ->options(
                        ClassRoom::with(['major'])
                            ->get()
                            ->mapWithKeys(fn($class) => [
                                $class->id => $class->level . ' ' . $class->turma . ' (' . $class->major->name . ')'
                            ])
                            ->toArray()
                    )
                    ->searchable()
                    ->multiple()
                    ->preload(),

                // Filter Jurusan - SINGLE SELECT
                Tables\Filters\SelectFilter::make('major')
                    ->relationship('classRoom.major', 'name')
                    ->label('Jurusan')
                    ->searchable()
                    ->preload(),

                // Filter Bulan & Tahun - COMPACT
                Tables\Filters\Filter::make('month_year')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('month')
                                    ->label('Bulan')
                                    ->options([
                                        '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
                                        '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agu',
                                        '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des',
                                    ])
                                    ->placeholder('Bulan'),
                                Forms\Components\Select::make('year')
                                    ->label('Tahun')
                                    ->options(
                                        collect(range(now()->year, now()->year - 3))
                                            ->mapWithKeys(fn($year) => [$year => $year])
                                            ->toArray()
                                    )
                                    ->placeholder('Tahun')
                                    ->default(now()->year),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['month'] ?? null,
                                fn (Builder $query, $month): Builder => $query->whereMonth('date', $month),
                            )
                            ->when(
                                $data['year'] ?? null,
                                fn (Builder $query, $year): Builder => $query->whereYear('date', $year),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['month'] ?? null) {
                            $monthNames = [
                                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                            ];
                            $indicators[] = Tables\Filters\Indicator::make('Bulan: ' . ($monthNames[$data['month']] ?? $data['month']));
                        }
                        if ($data['year'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Tahun: ' . $data['year']);
                        }
                        return $indicators;
                    }),

                // Filter Tanggal - SIMPLE VERSION
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['end_date'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['start_date'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Dari: ' . date('d M Y', strtotime($data['start_date'])));
                        }
                        if ($data['end_date'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Sampai: ' . date('d M Y', strtotime($data['end_date'])));
                        }
                        return $indicators;
                    }),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent) // FILTER DI ATAS TABEL
            ->filtersFormColumns(2) // 2 KOLOM UNTUK FORM FILTER
            ->actions([
                Tables\Actions\EditAction::make()
                    ->color('primary'),
                Tables\Actions\DeleteAction::make()
                    ->color('danger'),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->fileName('Absencia_' . date('Y-m-d_H-i-s'))
                    ->defaultFormat('pdf')
                    ->color('success')
                    ->label('Export PDF')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
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