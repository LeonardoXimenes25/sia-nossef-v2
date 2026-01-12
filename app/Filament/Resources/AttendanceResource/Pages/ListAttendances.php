<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;
    protected static ?string $title = 'Lista Absensia';

    /**
     * Header actions (tombol di atas table)
     */
    protected function getHeaderActions(): array
    {
        return [
            // Tombol untuk tambah data absensi
            Actions\CreateAction::make()
                ->label('Aumenta Dadus'),

            // Tombol cetak absensi kosong
            Actions\Action::make('print_blank')
                ->label('Cetak Absensi Kosong')
                ->icon('heroicon-o-printer')
                ->button()
                ->extraAttributes(['target' => '_blank'])
                ->url(fn () => $this->getPrintUrl())
                ->color('secondary'),
        ];
    }

    /**
     * Filter default saat halaman dibuka
     */
    protected function getDefaultTableFilters(): array
    {
        return [
            'date' => [
                'date' => now()->toDateString(),
            ],
        ];
    }

    /**
     * Mendapatkan URL untuk tombol cetak
     */
    protected function getPrintUrl(): string
    {
        $filters = $this->getTableFilters();
        $classRoomId = $filters['class_room_id'] ?? null;
        $subjectAssignmentId = $filters['subject_assignment_id'] ?? null;

        if (!$classRoomId || !$subjectAssignmentId) {
            // Kalau filter belum lengkap, tombol tetap clickable tapi beri alert
            return "javascript:alert('Pilih Klasse dan Disciplina terlebih dahulu.');";
        }

        // Filter lengkap → buka PDF
        return route('attendance.print.blank', [
            'class_room_id' => $classRoomId,
            'subject_assignment_id' => $subjectAssignmentId,
        ]);
    }
}
