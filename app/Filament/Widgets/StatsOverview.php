<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\ClassRoom;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;


class StatsOverview extends BaseWidget
{
    protected static bool $isLazy = false;
    // school stats
    protected function getStats(): array
    {
        return [
            Stat::make('Total Estudante', Student::count())
                ->color('success'),
            Stat::make('Total Professor', Teacher::count())
                ->color('warning'),
            Stat::make('Total Disiplina', Subject::count())
                ->color('primary'),
            Stat::make('Total Klasse', ClassRoom::count())
                ->color('primary'),
        ];
    }
}
