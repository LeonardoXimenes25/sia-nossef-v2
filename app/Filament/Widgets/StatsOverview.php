<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\ClassRoom;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = -100;
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Estudante', Student::count())
                ->color('success')
                ->url(route('filament.admin.resources.students.index')),

            Stat::make('Total Professor', Teacher::count())
                ->color('warning')
                ->url(route('filament.admin.resources.teachers.index')),

            Stat::make('Total Disiplina', Subject::count())
                ->color('primary')
                ->url(route('filament.admin.resources.subjects.index')),

            Stat::make('Total Klasse', ClassRoom::count())
                ->color('primary')
                ->url(route('filament.admin.resources.class-rooms.index')),
        ];
    }
}
