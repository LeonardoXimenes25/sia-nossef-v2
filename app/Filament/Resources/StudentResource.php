<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Imports\StudentsImport;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\FiltersLayout;
use App\Filament\Resources\StudentResource\Pages;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Estudante';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                /** Card: Informasi Pribadi */
                Forms\Components\Card::make([
                    Forms\Components\Section::make('Informasi Pessoal')
                        ->schema([
                            TextInput::make('nre')->label('ID Estudante')->required()->unique(ignoreRecord: true),
                            TextInput::make('name')->label('Naran Estudante')->required(),
                            Select::make('sex')
                                ->label('Sexu')
                                ->options(['m' => 'Mane', 'f' => 'Feto'])
                                ->required(),
                            Forms\Components\DatePicker::make('birth_date')
                                ->label('Data Moris')
                                ->required()
                                ->displayFormat('d-m-Y')
                                ->native(false)
                                ->minDate('1990-01-01')
                                ->maxDate(now()),
                            TextInput::make('birth_place')->label('Fatin Moris')->required(),
                            Forms\Components\Textarea::make('address')->label('Enderesu'),
                            TextInput::make('parent_name')->label('Naran Inan/Aman'),
                            TextInput::make('parent_contact')->label('Nu. Kontaktu Inan/Aman'),
                            Forms\Components\FileUpload::make('photo')
                                ->label('Imagen')
                                ->image()
                                ->directory('students/photos'),
                        ])->columns(2),
                ]),

                /** Card: Informasi Sekolah */
                Forms\Components\Card::make([
                    Forms\Components\Section::make('Informasaun Eskola')
                        ->schema([
                            Select::make('class_room_id')
                                ->label('Klasse / Turma')
                                ->options(function () {
                                    return \App\Models\ClassRoom::with('major')->get()->mapWithKeys(function ($classRoom) {
                                        $majorName = $classRoom->major ? $classRoom->major->name : '';
                                        return [$classRoom->id => $classRoom->level . ' ' . $majorName . ' ' . $classRoom->turma];
                                    });
                                })
                                ->searchable()
                                ->required(),

                            Select::make('major_id')->label('Area Estudu')->relationship('major', 'name')->required(),
                            Select::make('status')
                                ->label('Status')
                                ->options(['active' => 'Aktivu','alumni' => 'Alumni','left' => 'Sai'])
                                ->required(),
                            TextInput::make('admission_year')->label('Tinan Entrada')->numeric(),
                            TextInput::make('province')->label('Munisipiu'),
                            TextInput::make('district')->label('Posto'),
                            TextInput::make('subdistrict')->label('Suku'),
                        ])->columns(2),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Nu')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('nre')->label('ID Estudante')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->label('Naran Estudante')->sortable(),
                Tables\Columns\TextColumn::make('sex_text')->label('Sexu'),
                Tables\Columns\TextColumn::make('classRoom.level')->label('Klasse'),
                Tables\Columns\TextColumn::make('classRoom.turma')->label('Turma'),
                Tables\Columns\TextColumn::make('major.code')
                    ->label('Area Estudu')
                    ->extraAttributes(function ($state) {
                        $colors = [
                            'text-red-600',
                            'text-blue-600',
                            'text-green-600',
                            'text-yellow-600',
                            'text-purple-600',
                            'text-pink-600',
                        ];

                        $index = crc32($state) % count($colors);

                        return ['class' => $colors[$index] . ' font-bold'];
                    }),
                Tables\Columns\TextColumn::make('admission_year')->label('Tinan Entrada'),
                Tables\Columns\TextColumn::make('status_text')->label('Status'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'active' => 'Aktivu',
                    'alumni' => 'Alumni',
                    'left' => 'Sai',
                ]),

                Tables\Filters\Filter::make('major_id')
                    ->label('Area Estudu')
                    ->form([
                        Select::make('major_id')->label('Area Estudu')->relationship('major', 'name')->required(),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when(
                            $data['major_id'] ?? null,
                            fn($q, $majorId) => $q->where('major_id', $majorId)
                        );
                    }),
                    
                Tables\Filters\Filter::make('admission_year')
                    ->label('Tinan Entrada')
                    ->form([TextInput::make('admission_year')->label('Tinan Entrada')->numeric()])
                    ->query(function ($query, array $data) {
                        return $query->when(
                            $data['admission_year'] ?? null,
                            fn($q, $year) => $q->where('admission_year', $year)
                        );
                    }),
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
            ->headerActions([
                Action::make('import')
                    ->label('Import File')
                    ->color('danger')
                    ->icon('heroicon-o-users')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('Pilih File Excel/CSV')
                            ->required()
                            ->disk('public')
                            ->directory('imports')
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'text/csv',
                            ]),
                    ])
                    ->action(function (array $data) {
                        $filePath = storage_path('app/public/' . $data['file']);
                        Excel::import(new StudentsImport, $filePath);
                        \Filament\Notifications\Notification::make()
                            ->title('Data siswa berhasil di-upload')
                            ->success()
                            ->send();
                    }),

                FilamentExportHeaderAction::make('export')
                    ->fileName('Lista-Estudante')
                    ->defaultFormat('pdf')
                    ->color('success'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}