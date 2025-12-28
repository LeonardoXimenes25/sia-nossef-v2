<?php

namespace App\Filament\Resources\PeriodResource\Pages;

use Filament\Actions;
use App\Models\Period;
use App\Filament\Resources\PeriodResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePeriod extends CreateRecord
{
    protected static string $resource = PeriodResource::class;
    protected static ?string $title = 'Kria Períodu';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        if ($this->record->is_active) {
            Period::where('academic_year_id', $this->record->academic_year_id)
                ->where('id', '!=', $this->record->id)
                ->update(['is_active' => false]);
        }
    }

}
