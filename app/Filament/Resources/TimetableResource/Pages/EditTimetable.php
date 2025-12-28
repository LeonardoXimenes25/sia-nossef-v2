<?php

namespace App\Filament\Resources\TimetableResource\Pages;

use App\Models\Period;
use App\Models\AcademicYear;
use App\Filament\Resources\TimetableResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTimetable extends EditRecord
{
    protected static string $resource = TimetableResource::class;
    protected static ?string $title = 'Edita Horario';

    protected function mutateFormDataBeforeSave(array $data): array
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

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
