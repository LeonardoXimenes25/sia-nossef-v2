<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubjectResource\Pages;
use App\Models\Major;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;

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
        $user = Auth::user();
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
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([])
            ->headerActions($isStudent ? [] : [
                FilamentExportHeaderAction::make('export')
                    ->fileName('Lista-Disiplina')
                    ->defaultFormat('pdf')
                    ->color('success'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Edita')->hidden(fn() => $isStudent),
                Tables\Actions\DeleteAction::make()->label('Apaga')->hidden(fn() => $isStudent),
            ])
            ->bulkActions($isStudent ? [] : [
                Tables\Actions\DeleteBulkAction::make(),
            ])
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
            'edit' => Pages\EditSubject::route('/{record}/edit'),
        ];
    }

    /* =====================================================
     | QUERY
     ===================================================== */
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
{
    $query = parent::getEloquentQuery();

    $user = Auth::user();
    if ($user && $user->hasRole('estudante')) {
        $student = $user->student;
        if ($student) {
            // Filter subject sesuai major siswa
            $query->whereHas('majors', fn($q) => $q->where('id', $student->major_id));
        }
    }

    return $query;
}

}
