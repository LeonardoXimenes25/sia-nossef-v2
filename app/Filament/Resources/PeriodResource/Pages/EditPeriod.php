<?php

namespace App\Filament\Resources\PeriodResource\Pages;

use Filament\Actions;
use App\Models\Period;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\PeriodResource;

class EditPeriod extends EditRecord
{
    protected static string $resource = PeriodResource::class;
    protected static ?string $title = 'Edita Períudu';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function beforeSave(): void
    {
        if ($this->data['is_active']) {
            Period::where('academic_year_id', $this->data['academic_year_id'])
                ->where('id', '!=', $this->record->id)
                ->update(['is_active' => false]);
        }
    }

}
