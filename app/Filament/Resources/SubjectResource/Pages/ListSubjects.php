<?php

namespace App\Filament\Resources\SubjectResource\Pages;

use App\Filament\Resources\SubjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubjects extends ListRecords
{
    protected static string $resource = SubjectResource::class;
    protected static ?string $title = 'Lista Disiplina';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Aumenta Dadus'),
        ];
    }
}
