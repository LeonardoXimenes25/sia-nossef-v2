<?php

namespace App\Filament\Resources\MajorResource\Pages;

use App\Filament\Resources\MajorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMajors extends ListRecords
{
    protected static string $resource = MajorResource::class;
    protected static ?string $title = 'Lista Area Estudu';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Aumenta Dadus'),
        ];
    }
}
