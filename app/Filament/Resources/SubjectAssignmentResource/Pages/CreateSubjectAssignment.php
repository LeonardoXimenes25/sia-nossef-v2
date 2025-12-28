<?php

namespace App\Filament\Resources\SubjectAssignmentResource\Pages;

use App\Filament\Resources\SubjectAssignmentResource;
use App\Models\SubjectAssignment;
use App\Models\AcademicYear;
use App\Models\Period;
use Filament\Resources\Pages\CreateRecord;

class CreateSubjectAssignment extends CreateRecord
{
    protected static string $resource = SubjectAssignmentResource::class;
    protected static ?string $title = 'Kria Alokasaun Disiplina';

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Get active academic year and period
        $activeYear = AcademicYear::where('is_active', true)->first();
        $activePeriod = Period::where('is_active', true)
            ->where('academic_year_id', $activeYear?->id)
            ->first();

        $record = SubjectAssignment::create([
            'teacher_id' => $data['teacher_id'],
            'academic_year_id' => $activeYear?->id,
            'period_id' => $activePeriod?->id,
            'is_active' => $data['is_active'] ?? true,
        ]);

        $record->subjects()->sync($data['subjects'] ?? []);
        $record->classRooms()->sync($data['classRooms'] ?? []);

        return $record;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Disiplina alokasaun konsege rejistu';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
