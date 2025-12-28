<?php

namespace App\Filament\Resources\AcademicYearResource\Pages;

use App\Filament\Resources\AcademicYearResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAcademicYears extends ListRecords
{
    protected static string $resource = AcademicYearResource::class;
    protected static ?string $title = 'Lista Ano Letivo';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Aumenta Dadus'),
        ];
    }
}
