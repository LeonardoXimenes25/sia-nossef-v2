<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\ClassRoom;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Filament\Resources\Resource;
use App\Filament\Resources\ClassRoomResource\Pages;

class ClassRoomResource extends Resource
{
    protected static ?string $model = ClassRoom::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Managementu Akademiku';
    protected static ?string $navigationLabel = 'Klasse';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('level')
                    ->label('Klasse')
                    ->placeholder('10, 11, 12')
                    ->required()
                    ->numeric(),

                Forms\Components\Select::make('major_id')
                    ->label('Area Estudu')
                    ->relationship('major', 'name')
                    ->required(),

                Forms\Components\TextInput::make('turma')
                    ->label('Turma')
                    ->placeholder('A, B, C')
                    ->required()
                    ->maxLength(1)
                    ->rule(function ($record, $get) {
                        return Rule::unique('class_rooms', 'turma')
                            ->where('level', $get('level'))
                            ->where('major_id', $get('major_id'))
                            ->ignore($record?->id);
                    })
                    ->validationMessages([
                        'unique' => 'Kombinasaun Klasse, Area Estudu no Turma ida ne\'e ona iha.',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Nu')->sortable(),
                Tables\Columns\TextColumn::make('level')->label('Klasse')->sortable(),
                Tables\Columns\TextColumn::make('major.name')
                    ->label('Area Estudu')
                    ->sortable()
                    ->color(fn ($record) => $record->major_id == 1 ? 'success' : 'warning')
                    ->badge(),
                Tables\Columns\TextColumn::make('turma')->label('Turma')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Edita'),
                Tables\Actions\DeleteAction::make()->label('Apaga'),
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
            'index' => Pages\ListClassRooms::route('/'),
            'create' => Pages\CreateClassRoom::route('/create'),
            'edit' => Pages\EditClassRoom::route('/{record}/edit'),
        ];
    }
}