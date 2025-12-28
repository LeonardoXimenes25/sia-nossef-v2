<?php

namespace App\Filament\Resources\TimetableResource\Pages;

use App\Models\Period;
use App\Models\AcademicYear;
use App\Filament\Resources\TimetableResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTimetable extends CreateRecord
{
    protected static string $resource = TimetableResource::class;
    protected static ?string $title = 'Kria Horario';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Get the active academic year
        $activeYear = AcademicYear::where('is_active', true)->first();
        
        // Get the active period for this academic year
        $activePeriod = Period::where('is_active', true)
            ->where('academic_year_id', $activeYear->id)
            ->first();

        // Set the academic_year_id and period_id
        $data['academic_year_id'] = $activeYear->id;
        $data['period_id'] = $activePeriod->id;

        // If logged-in user is a teacher, auto-fill their first subject assignment
        $user = Auth::user();
        if ($user && $user->teacher) {
            $subjectAssignment = $user->teacher->subjectAssignments()
                ->where('is_active', true)
                ->first();
            
            if ($subjectAssignment) {
                $data['subject_assignment_id'] = $subjectAssignment->id;
            }
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
