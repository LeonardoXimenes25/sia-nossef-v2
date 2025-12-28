<?php

namespace App\Filament\Resources\AcademicYearResource\Pages;

use Filament\Actions;
use App\Models\AcademicYear;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\AcademicYearResource;

class EditAcademicYear extends EditRecord
{
    protected static string $resource = AcademicYearResource::class;
    protected static ?string $title = 'Edita Ano Letivo';

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
        if ($this->data['is_active'] ?? false) {
            AcademicYear::where('id', '!=', $this->record->id)
                ->update(['is_active' => false]);
        }
    }
}
