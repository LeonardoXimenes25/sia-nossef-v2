<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttendance extends EditRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Override redirect after update
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Success notification message
     */
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Absénsia atualiza ho suksesu';
    }

    /**
     * Mutate form data before fill
     * Konversi dari single record ke format repeater
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Untuk edit, kita tidak menggunakan repeater
        // Jadi kita hanya edit record tunggal
        return $data;
    }
}