<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use Filament\Widgets\ChartWidget;

class GenderByMajorCTChart extends ChartWidget
{
    protected static ?string $heading = 'Distribuisaun Jeneru Siensia Teknolojia';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '250px'; // lebih kecil dari default

    protected function getData(): array
    {
        $male = Student::where('sex', 'm')
            ->whereHas('classRoom.major', fn ($q) => $q->where('code', 'CT'))
            ->count();

        $female = Student::where('sex', 'f')
            ->whereHas('classRoom.major', fn ($q) => $q->where('code', 'CT'))
            ->count();

        return [
            'datasets' => [
                [
                    'data' => [$male, $female],
                    'backgroundColor' => ['#1E90FF', '#FF69B4'], // warna slice
                    'borderRadius' => 5,
                ],
            ],
            'labels' => ['Mane', 'Feto'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
                'datalabels' => [ // tampilkan angka
                    'color' => '#000',
                    'font' => [
                        'weight' => 'bold',
                        'size' => 14,
                    ],
                    'formatter' => function ($value) {
                        return $value;
                    },
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => false,
                    'grid' => ['display' => false, 'drawBorder' => false],
                ],
                'y' => [
                    'display' => false,
                    'grid' => ['display' => false, 'drawBorder' => false],
                ],
            ],
        ];
    }
}
