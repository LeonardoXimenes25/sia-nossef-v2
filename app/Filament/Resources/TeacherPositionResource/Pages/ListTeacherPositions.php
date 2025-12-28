<?php

namespace App\Filament\Resources\TeacherPositionResource\Pages;

use App\Filament\Resources\TeacherPositionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTeacherPositions extends ListRecords
{
    protected static string $resource = TeacherPositionResource::class;
    protected static ?string $title = 'Lista Pozisaun Professor';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Aumenta Dadus'),
        ];
    }
}
