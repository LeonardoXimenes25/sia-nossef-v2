<?php

namespace App\Filament\Resources\GradeResource\Pages;

use Filament\Actions;
use App\Filament\Widgets\GradeStats;
use App\Filament\Resources\GradeResource;
use Filament\Resources\Pages\ListRecords;

class ListGrades extends ListRecords
{
    protected static string $resource = GradeResource::class;
    protected static ?string $title = 'Lista Valor';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Aumenta Dadus'),
        ];
    }

     protected function getHeaderWidgets(): array
    {
        return [
            GradeStats::class,
        ];
    }
}
