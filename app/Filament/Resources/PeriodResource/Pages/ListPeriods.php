<?php

namespace App\Filament\Resources\PeriodResource\Pages;

use App\Filament\Resources\PeriodResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPeriods extends ListRecords
{
    protected static string $resource = PeriodResource::class;
    protected static ?string $title = 'Lista Períudu';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Aumenta Dadus'),
        ];
    }
}
