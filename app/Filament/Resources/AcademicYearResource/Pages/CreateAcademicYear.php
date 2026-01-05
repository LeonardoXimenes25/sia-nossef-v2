<?php

namespace App\Filament\Resources\AcademicYearResource\Pages;

use Filament\Actions;
use App\Models\AcademicYear;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\AcademicYearResource;

class CreateAcademicYear extends CreateRecord
{
    protected static string $resource = AcademicYearResource::class;
    protected static ?string $title = 'Kria Tinan Letivo';
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        if ($this->record->is_active) {
            AcademicYear::where('id', '!=', $this->record->id)
                ->update(['is_active' => false]);
        }
    }
}
