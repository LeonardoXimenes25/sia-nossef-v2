<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Student;
use Filament\Forms\Form;
use App\Models\ClassRoom;
use Filament\Tables\Table;
use App\Imports\StudentsImport;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\StudentResource\Pages;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Estudante';
    protected static ?string $navigationGroup = 'Managementu Akademiku';

    // Batasi query untuk estudante
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['classRoom.major']);
        
        $user = Auth::user();
        if ($user && $user->hasRole('estudante')) {
            $student = $user->student;
            if ($student) {
                $query->where('id', $student->id);
            }
        }

        return $query;
    }

    protected static function getClassroomOptions(): array
    {
        return ClassRoom::with('major')
            ->get()
            ->mapWithKeys(fn($c) => [
                $c->id => $c->level . ' ' . $c->turma . ' (' . ($c->major?->name ?? '') . ')'
            ])
            ->toArray();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make([
                    Forms\Components\Section::make('Informasi Pessoal')
                        ->schema([
                            Forms\Components\TextInput::make('nre')
                                ->label('ID Estudante')
                                ->placeholder('Prenxe ID Estudante')
                                ->required()
                                ->unique(ignoreRecord: true),
                            Forms\Components\TextInput::make('name')
                                ->label('Naran Estudante')
                                ->placeholder('Prenxe Naran Estudante')
                                ->required(),
                            Forms\Components\Select::make('sex')
                                ->label('Sexu')
                                ->options(['m'=>'Mane','f'=>'Feto'])
                                ->placeholder('Hili Sexu')
                                ->required(),
                            Forms\Components\DatePicker::make('birth_date')
                                ->label('Data Moris')
                                ->required()
                                ->placeholder('Prenxe Data Moris'),
                            Forms\Components\TextInput::make('birth_place')
                                ->label('Fatin Moris')
                                ->required()
                                ->placeholder('Prenxe Fatin Moris'),
                            Forms\Components\Textarea::make('address')
                                ->label('Hela Fatin')
                                ->placeholder('Prenxe hela fatin'),
                            Forms\Components\TextInput::make('parent_name')
                                ->label('Naran Inan/Aman')
                                ->placeholder('Prenxe naran inan/aman'),
                            Forms\Components\TextInput::make('parent_contact')
                                ->label('Nu. Kontaktu Inan/Aman')
                                ->placeholder('Prenxe nu. kontaktu inan/aman'),
                            Forms\Components\FileUpload::make('photo')
                                ->label('Imagen')
                                ->image()
                                ->directory('students/photos'),
                        ])->columns(2),
                ]),
                Forms\Components\Card::make([
                    Forms\Components\Section::make('Informasaun Eskola')
                        ->schema([
                            Forms\Components\Select::make('class_room_id')
                                ->label('Klasse / Turma')
                                ->options(self::getClassroomOptions())
                                ->searchable()
                                ->required(),
                            Forms\Components\Select::make('status')
                                ->label('Status')
                                ->options([
                                    'active'=>'Aktivu',
                                    'alumni'=>'Alumni',
                                    'left'=>'Sai'])
                                ->required(),
                            Forms\Components\TextInput::make('admission_year')->label('Tinan Entrada')->numeric(),
                            Forms\Components\TextInput::make('province')->label('Munisipiu'),
                            Forms\Components\TextInput::make('district')->label('Posto'),
                            Forms\Components\TextInput::make('subdistrict')->label('Suku'),
                        ])->columns(2),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = Auth::user();
        $isEstudante = $user?->hasRole('estudante');

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Nu')->sortable(),
                Tables\Columns\TextColumn::make('nre')->label('ID Estudante')->sortable()
                    ->searchable(!$isEstudante),
                Tables\Columns\TextColumn::make('name')->label('Naran Estudante')->sortable()
                    ->searchable(!$isEstudante),
                Tables\Columns\TextColumn::make('sex')->label('Sexu')->sortable()
                    ->searchable(!$isEstudante),
                Tables\Columns\TextColumn::make('classRoom.level')->label('Klasse')->sortable()
                    ->searchable(!$isEstudante),
                Tables\Columns\TextColumn::make('classRoom.turma')->label('Turma')->sortable()
                    ->searchable(!$isEstudante),
                Tables\Columns\TextColumn::make('classRoom.major.code')->label('Area Estudu')->badge()->color(fn($record) => match($record->classRoom?->major?->code) {
                    'CT' => 'success',
                    'CSH' => 'primary',
                    default => 'secondary',
                })->sortable()->searchable(!$isEstudante),
                Tables\Columns\TextColumn::make('admission_year')->label('Tinan Entrada')->sortable()
                    ->searchable(!$isEstudante),
                Tables\Columns\TextColumn::make('status')->label('Status')->sortable()
                    ->searchable(!$isEstudante),
            ])
            ->filters($isEstudante ? [] : [
                Tables\Filters\SelectFilter::make('class_room_id')
                    ->label('Kelas / Turma')
                    ->options(self::getClassroomOptions())
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Aktivu',
                        'alumni' => 'Alumni',
                        'left' => 'Sai',
                    ])
                    ->label('Status')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('admission_year')
                    ->form([Forms\Components\TextInput::make('admission_year')->numeric()])
                    ->query(fn(Builder $query, array $data) => $query->when($data['admission_year'] ?? null, fn($q, $year) => $q->where('admission_year', $year)))
                    ->label('Tinan Entrada'),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make()->label('Edita'),
                Tables\Actions\DeleteAction::make()->label('Apaga'),
            ])
            ->headerActions($isEstudante ? [] : [
                Action::make('import')->label('Import File')->color('danger')->icon('heroicon-o-users')
                    ->form([
                        Forms\Components\FileUpload::make('file')->label('Pilih File Excel/CSV')->required()
                            ->disk('public')->directory('imports')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','text/csv']),
                    ])
                    ->action(function (array $data) {
                        $filePath = storage_path('app/public/' . $data['file']);
                        Excel::import(new StudentsImport, $filePath);
                        \Filament\Notifications\Notification::make()
                            ->title('Data siswa berhasil di-upload')
                            ->success()
                            ->send();
                    }),

                FilamentExportHeaderAction::make('export')->fileName('Lista-Estudante')->defaultFormat('pdf')->color('success'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])
            ])
            ->defaultSort('id','asc');
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
