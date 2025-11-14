<?php

namespace App\Filament\Resources\SubjectAssignmentResource\Pages;

use App\Filament\Resources\SubjectAssignmentResource;
use Filament\Resources\Pages\EditRecord;

class EditSubjectAssignment extends EditRecord
{
    protected static string $resource = SubjectAssignmentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Setelah data disimpan, update juga pivot classRooms
     */
    protected function afterSave(): void
    {
        if (isset($this->data['classRooms'])) {
            $this->record->classRooms()->sync($this->data['classRooms']);
        }
    }
}
