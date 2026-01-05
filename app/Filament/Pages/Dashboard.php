<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\LatestTimetables;
use App\Filament\Widgets\StudentGender;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?int $navigationSort = -2;

    public function getHeading(): string
    {
        return 'Dashboard';
    }

    public function getTitle(): string
    {
        return 'Dashboard';
    }

    // ✅ HARUS PUBLIC
    public function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }

    // ✅ HARUS PUBLIC (INI ERROR KAMU)
    public function getWidgets(): array
    {
        return [
            LatestTimetables::class,
            StudentGender::class,
        ];
    }

    // ✅ HARUS PUBLIC
    public function getAllWidgets(): array
    {
        return [
            StatsOverview::class,
            LatestTimetables::class,
            StudentGender::class,
        ];
    }
}
