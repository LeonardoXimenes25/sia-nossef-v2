<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherPositionResource\Pages;
use App\Models\TeacherPosition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TeacherPositionResource extends Resource
{
    protected static ?string $model = TeacherPosition::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Posisaun Professor';
    protected static ?string $navigationGroup = 'Managementu Professores';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Naran Posisaun')
                    ->required()
                    ->maxLength(255),

                // Level hidden, otomatis diisi
                Forms\Components\Hidden::make('level')
                    ->default(function () {
                        return TeacherPosition::count() + 1;
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('level')->label('Nu.'),
                Tables\Columns\TextColumn::make('name')->label('Naran Posisaun'),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeacherPositions::route('/'),
            'create' => Pages\CreateTeacherPosition::route('/create'),
            'edit' => Pages\EditTeacherPosition::route('/{record}/edit'),
        ];
    }
}
