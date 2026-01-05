<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use Filament\Widgets\ChartWidget;

class StudentGender extends ChartWidget
{
    protected static ?string $heading = 'Distribuisaun Estudante por Jenru';
    protected static ?string $maxHeight = '220px';
    protected static ?int $sort = 1; // tampil setelah LatestTimetables


    protected function getData(): array
    {
        return [
            $male = Student::where('sex', 'm')->count(),
            $female = Student::where('sex', 'f')->count(),
            'labels' => ['Mane', 'Feto'],
            'datasets' => [
                [
                    'label' => 'Estudante por Jenru',
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
