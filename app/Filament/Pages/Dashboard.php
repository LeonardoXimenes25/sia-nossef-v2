<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?int $navigationSort = -3;

    public function getHeading(): string
    {
        return 'Dashboard';
    }

    public function getTitle(): string
    {
        return 'Dashboard';
    }


    /**
     * BODY DASHBOARD
     */
    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\LatestTimetables::class,     // Stats di tengah
            \App\Filament\Widgets\StudentGender::class,     // Gender chart di bawah
            \App\Filament\Widgets\LatestTimetables::class, 
        ];
    }
}