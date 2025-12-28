<?php

namespace App\Filament\Resources\NewsCategoryResource\Pages;

use App\Filament\Resources\NewsCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNewsCategories extends ListRecords
{
    protected static string $resource = NewsCategoryResource::class;
    protected static ?string $title = 'Lista Kategoria Notisia';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Aumenta Dadus'),
        ];
    }
}
