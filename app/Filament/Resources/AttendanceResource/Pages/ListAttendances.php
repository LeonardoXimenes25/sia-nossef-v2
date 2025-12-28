<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;
    protected static ?string $title = 'Lista Absensia';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Aumenta Dadus'),
        ];
    }

    protected function getDefaultTableFilters(): array
    {
        return [
            'date' => [
                'date' => now()->toDateString(),
            ],
        ];
    }
}
