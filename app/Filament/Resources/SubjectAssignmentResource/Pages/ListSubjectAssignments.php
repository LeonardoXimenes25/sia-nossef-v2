<?php

namespace App\Filament\Resources\SubjectAssignmentResource\Pages;

use App\Filament\Resources\SubjectAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubjectAssignments extends ListRecords
{
    protected static string $resource = SubjectAssignmentResource::class;
    protected static ?string $title = 'Lista Alokasaun Disiplina';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Aumenta Dadus'),
        ];
    }
}
