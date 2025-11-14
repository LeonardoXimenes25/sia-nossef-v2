<?php

namespace App\Filament\Resources\SubjectAssignmentResource\Pages;

use App\Filament\Resources\SubjectAssignmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSubjectAssignment extends CreateRecord
{
    protected static string $resource = SubjectAssignmentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Setelah record SubjectAssignment dibuat,
     * kita sinkronkan relasi classRooms ke tabel pivot
     */
    protected function afterCreate(): void
    {
        // Pastikan data 'classRooms' ada sebelum sync
        if (isset($this->data['classRooms'])) {
            $this->record->classRooms()->sync($this->data['classRooms']);
        }
    }
}
