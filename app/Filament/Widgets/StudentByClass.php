<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use App\Models\ClassRoom;
use Filament\Widgets\ChartWidget;

class StudentByClass extends ChartWidget
{
    protected static ?string $heading = 'Total Estudante Kada Klasse';
    protected static ?string $maxHeight = '400px'; // ukuran chart
    protected static ?int $sort = 1; // tampil setelah LatestTimetables


    protected function getData(): array
{
    // Ambil semua class room + major-nya
    $classRooms = ClassRoom::with('major')
        ->orderBy('level')
        ->orderBy('major_id')
        ->orderBy('turma')
        ->get();

    // Labels chart = level - major - turma
    $labels = $classRooms->map(function ($room) {
        $majorCode = $room->major ? $room->major->code : 'Sem Major';
        return "{$room->level} {$majorCode} {$room->turma}";
    })->toArray();

    // Hitung jumlah siswa pada setiap class_room_id
    $data = $classRooms->map(function ($room) {
        return Student::where('class_room_id', $room->id)->count();
    })->toArray();

    return [
        'datasets' => [
            [
                'label' => 'Total Alunos',
                'data' => $data,
                'backgroundColor' => '#00BFFF',
                'borderRadius' => 10,
            ],
        ],
        'labels' => $labels,
    ];
}

protected function getOptions(): array
{
    return [
        'plugins' => [
            'legend' => [
                'display' => false,
            ],
        ],

        'scales' => [
            'x' => [
                'grid' => [
                    'display' => false,
                    'drawBorder' => false,
                ],
            ],
            'y' => [
                'grid' => [
                    'display' => false,
                    'drawBorder' => false,
                ],
            ],
        ],
    ];
}



    protected function getType(): string
    {
        return 'bar';
    }
}
