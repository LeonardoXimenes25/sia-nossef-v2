<?php

namespace App\Filament\Resources\TeacherResource\Pages;

use App\Filament\Resources\TeacherResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTeachers extends ListRecords
{
    protected static string $resource = TeacherResource::class;
    protected static ?string $title = 'Lista Mestre';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Aumenta Dadus'),
        ];
    }
}
