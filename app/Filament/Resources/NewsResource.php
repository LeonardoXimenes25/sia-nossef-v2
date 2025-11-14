<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Filament\Resources\NewsResource\RelationManagers;
use App\Models\News;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Portal Informasaun';
    protected static ?string $navigationLabel = 'Publikasaun';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Titulu')
                    ->reactive()
                    ->required(),
                
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Kategoria')
                    ->required(),

                Forms\Components\RichEditor::make('content')
                    ->label('Konteudu'),

                Forms\Components\FileUpload::make('image')
                    ->label('Imajen'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Textcolumn::make('id')->label('Nu'),
                Tables\Columns\Textcolumn::make('title')->label('Titulu'),
                Tables\Columns\Textcolumn::make('content')
                    ->label('Konteudu')
                    ->wrap(),
                Tables\Columns\Imagecolumn::make('image')
                    ->label('Imajen')
                    ->square(),
                Tables\Columns\TextColumn::make('created_at')->date('d-m-y'),
                Tables\Columns\TextColumn::make('updated_at')->date('d-m-y'),

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
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
