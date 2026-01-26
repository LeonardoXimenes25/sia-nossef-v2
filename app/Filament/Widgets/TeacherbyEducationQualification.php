<?php

namespace App\Filament\Widgets;

use App\Models\Teacher;
use Filament\Widgets\ChartWidget;

class TeacherbyEducationQualification extends ChartWidget
{
    protected static ?string $heading = 'Distribuisaun Abilitasaun Literaria Professor';
    protected static ?string $maxHeight = '220px';
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        // Ambil data dan kelompokkan berdasarkan kualifikasi pendidikan
        $data = Teacher::query()
            ->selectRaw('educational_qualification, COUNT(*) as total')
            ->groupBy('educational_qualification')
            ->get();

        return [
            'labels' => $data->pluck('educational_qualification')->toArray(),
            'datasets' => [
                [
                    'label' => 'Kualifikasaun Edukasaun',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
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
