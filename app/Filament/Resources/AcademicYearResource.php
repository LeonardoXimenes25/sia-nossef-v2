<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcademicYearResource\Pages;
use App\Filament\Resources\AcademicYearResource\RelationManagers;
use App\Models\AcademicYear;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AcademicYearResource extends Resource
{
    protected static ?string $model = AcademicYear::class;

    protected static ?string $navigationGroup = 'Managementu Akademiku';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Ano Letivo';
    


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->label('Tinan Letivu')
                ->placeholder('Ezemplu: 2024/2025')
                ->required(),

                Forms\Components\DatePicker::make('start_date')
                    ->label('Data Hahu'),

                Forms\Components\DatePicker::make('end_date')
                    ->label('Data Remata'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Aktivu')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Tinan Letivu')->sortable(),
                Tables\Columns\TextColumn::make('start_date')->label('Data Hahu')->date('d-m-y'),
                Tables\Columns\TextColumn::make('end_date')->label('Data Remaata')->date('d-m-y'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Aktivu')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListAcademicYears::route('/'),
            'create' => Pages\CreateAcademicYear::route('/create'),
            'edit' => Pages\EditAcademicYear::route('/{record}/edit'),
        ];
    }
}
