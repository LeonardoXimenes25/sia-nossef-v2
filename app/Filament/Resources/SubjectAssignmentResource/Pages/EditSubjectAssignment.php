<?php

namespace App\Filament\Resources\SubjectAssignmentResource\Pages;

use App\Filament\Resources\SubjectAssignmentResource;
use App\Models\AcademicYear;
use App\Models\Period;
use Filament\Resources\Pages\EditRecord;

class EditSubjectAssignment extends EditRecord
{
    protected static string $resource = SubjectAssignmentResource::class;
    protected static ?string $title = 'Edita Alokasaun Disiplina';

    /**
     * Handle record update.
     */
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        // Get active academic year and period
        $activeYear = AcademicYear::where('is_active', true)->first();
        $activePeriod = Period::where('is_active', true)
            ->where('academic_year_id', $activeYear?->id)
            ->first();

        // Update main fields
        $record->update([
            'teacher_id' => $data['teacher_id'],
            'academic_year_id' => $activeYear?->id,
            'period_id' => $activePeriod?->id,
            'is_active' => $data['is_active'] ?? true,
        ]);

        // Sync pivot tables
        $record->subjects()->sync($data['subjects'] ?? $record->subjects->pluck('id')->toArray());
        $record->classRooms()->sync($data['classRooms'] ?? $record->classRooms->pluck('id')->toArray());

        return $record;
    }

    /**
     * Notification title after update.
     */
    protected function getUpdatedNotificationTitle(): ?string
    {
        return 'Disiplina alokasaun rejistu haktualizadu';
    }

    /**
     * Redirect after update.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
