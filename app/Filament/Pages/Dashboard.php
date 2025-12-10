<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';

    protected static ?int $navigationSort = -2;

    public function getHeading(): string
    {
        return 'Dashboard';
    }

    public function getTitle(): string
    {
        return 'Dashboard';
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\StatsOverview::class,
            \App\Filament\Widgets\StudentGender::class,
        ];
    }   
}