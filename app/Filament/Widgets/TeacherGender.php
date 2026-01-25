<?php

namespace App\Filament\Widgets;

use App\Models\Teacher;
use Filament\Widgets\ChartWidget;

class TeacherGender extends ChartWidget
{
    protected static ?string $heading = 'Distribuisaun Professor por Jenru';
    protected static ?string $maxHeight = '220px';
    protected static ?int $sort = 1; // tampil setelah LatestTimetables


    protected function getData(): array
    {
        return [
            $male = Teacher::where('gender', 'm')->count(),
            $female = Teacher::where('gender', 'f')->count(),
            'labels' => ['Mane', 'Feto'],
            'datasets' => [
                [
                    'label' => 'Professor por Jenru',
                    'data' => [$male, $female],
                    'backgroundColor' => [
                        'rgba(54, 162, 235, 0.7)', // Warna untuk Mane
                        'rgba(255, 99, 132, 0.7)', // Warna untuk Feto
                    ],
                    'borderColor' => [
                        'rgba(54, 162, 235, 1)', // Border Warna untuk Mane
                        'rgba(255, 99, 132, 1)', // Border Warna untuk Feto
                    ],
                    'borderWidth' => 1,
                ],
            ],                      
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
