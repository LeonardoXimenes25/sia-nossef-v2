<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Major;
use App\Models\Subject;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Resources\SubjectResource\Pages;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static ?string $navigationLabel = 'Disiplina';
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Managementu Akademiku';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Nama Mata Pelajaran
                Forms\Components\TextInput::make('name')
                    ->label('Naran Disiplina')
                    ->required(),

                // Pilih Jurusan (many-to-many)
                Forms\Components\Select::make('majors')
                    ->label('Area Estudu')
                    ->multiple()
                    ->relationship('majors', 'name') // relasi pivot
                    ->preload()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Nu. '),
                Tables\Columns\TextColumn::make('name')->label('Naran Disiplina')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('majors.name')->label('Area Estudu')
                    ->badge()
                    ->color(fn ($record) =>
                            $record->majors->count() > 1
                                ? 'success'   // hijau jika lebih dari satu
                                : 'primary'   // biru jika satu
                        )
                    ->searchable()
                    ->sortable(),

            ])
            ->filters([])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->fileName('Lista-Disiplina')
                    ->defaultFormat('pdf')
                    ->color('success'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'edit' => Pages\EditSubject::route('/{record}/edit'),
        ];
    }
}
