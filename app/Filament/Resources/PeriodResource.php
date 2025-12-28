<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeriodResource\Pages;
use App\Models\Period;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PeriodResource extends Resource
{
    protected static ?string $model = Period::class;

    protected static ?string $navigationGroup = 'Managementu Akademiku';
    protected static ?string $navigationLabel = 'Periodu';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    /* =====================================================
     |  FORM
     ===================================================== */
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('academic_year_id')
                ->label('Tinan Akademiku')
                ->relationship('academicYear', 'name')
                ->required(),

            Forms\Components\TextInput::make('name')
                ->label('Naran Periodu')
                ->placeholder('Ex: Primeiru, Segundu, Terceiru')
                ->required(),

            Forms\Components\TextInput::make('order')
                ->label('Ordem')
                ->numeric()
                ->required()
                ->default(1),

            Forms\Components\DatePicker::make('start_date')
                ->label('Data Hahu'),

            Forms\Components\DatePicker::make('end_date')
                ->label('Data Remata'),

            Forms\Components\Toggle::make('is_active')
                ->label('Aktivu')
                ->default(false),
        ]);
    }

    /* =====================================================
     |  TABLE
     ===================================================== */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('Ordem')
                    ->sortable(),

                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('Tinan Akademiku')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Naran Periodu')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktivu')
                    ->boolean(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Data Hahu')
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Data Remata')
                    ->date('d M Y'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPeriods::route('/'),
            'create' => Pages\CreatePeriod::route('/create'),
            'edit'   => Pages\EditPeriod::route('/{record}/edit'),
        ];
    }
}
