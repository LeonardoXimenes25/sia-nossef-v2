<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubjectResource\Pages;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use Illuminate\Database\Eloquent\Builder;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static ?string $navigationLabel = 'Disiplina';
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Managementu Akademiku';

    /* =====================================================
     | FORM
     ===================================================== */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Naran Disiplina')
                    ->required(),

                Forms\Components\Select::make('majors')
                    ->label('Area Estudu')
                    ->multiple()
                    ->relationship('majors', 'name')
                    ->preload()
                    ->required(),
            ]);
    }

    /* =====================================================
     | TABLE
     ===================================================== */
    public static function table(Table $table): Table
    {
        $user = filament()->auth()->user();
        $isStudent = $user?->hasRole('estudante');

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Nu.'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Naran Disiplina')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('majors.name')
                    ->label('Area Estudu')
                    ->badge()
                    ->color(fn ($record) =>
                        $record->majors->count() > 1 ? 'success' : 'primary'
                    )
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([])
            ->headerActions(
                $isStudent
                    ? []
                    : [
                        FilamentExportHeaderAction::make('export')
                            ->fileName('Lista-Disiplina')
                            ->defaultFormat('pdf')
                            ->color('success'),
                    ]
            )
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edita')
                    ->hidden(fn () => $isStudent),
                Tables\Actions\DeleteAction::make()
                    ->label('Apaga')
                    ->hidden(fn () => $isStudent),
            ])
            ->bulkActions(
                $isStudent
                    ? []
                    : [
                        Tables\Actions\DeleteBulkAction::make(),
                    ]
            )
            ->defaultSort('id', 'asc');
    }

    /* =====================================================
     | RELATIONS
     ===================================================== */
    public static function getRelations(): array
    {
        return [];
    }

    /* =====================================================
     | PAGES
     ===================================================== */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'edit'   => Pages\EditSubject::route('/{record}/edit'),
        ];
    }

    /* =====================================================
     | QUERY FILTER ROLE ESTUDANTE VIA CLASSROOM → MAJOR
     ===================================================== */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = filament()->auth()->user();

        if ($user?->hasRole('estudante')) {
            $student = $user->student;

            if ($student && $student->classRoom && $student->classRoom->major_id) {
                $majorId = $student->classRoom->major_id;

                $query->whereHas('majors', function ($q) use ($majorId) {
                    $q->where('majors.id', $majorId);
                });
            } else {
                // Jika student belum punya classroom atau classroom belum punya major
                $query->whereRaw('1 = 0'); // hasil kosong
            }
        }

        return $query;
    }
}
