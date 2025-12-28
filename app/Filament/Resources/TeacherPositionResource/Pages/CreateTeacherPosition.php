<?php

namespace App\Filament\Resources\TeacherPositionResource\Pages;

use App\Filament\Resources\TeacherPositionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTeacherPosition extends CreateRecord
{
    protected static string $resource = TeacherPositionResource::class;
    protected static ?string $title = 'Kria Pozisaun Professor';
}
