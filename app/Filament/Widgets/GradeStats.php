<?php

namespace App\Filament\Widgets;

use App\Models\Grade;
use App\Models\AcademicYear;
use App\Models\Period;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class GradeStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        // 🔹 Tahun & period aktif
        $activeYearId = AcademicYear::where('is_active', true)->value('id');

        $activePeriodId = Period::where('is_active', true)
            ->where('academic_year_id', $activeYearId)
            ->value('id');

        // 🔹 Base query
        $baseQuery = Grade::with(['student', 'classRoom', 'subject'])
            ->where('academic_year_id', $activeYearId)
            ->where('period_id', $activePeriodId)
            ->whereNotNull('score');

        $user = Auth::user();

        // 🔐 Filter sesuai role
        if ($user?->hasRole('estudante') && $user->student) {
            $baseQuery->where('student_id', $user->student->id);
        }

        if ($user?->hasRole('mestre') && $user->teacher) {
            $baseQuery->where('teacher_id', $user->teacher->id);
        }

        // 🔹 Ambil nilai max & min
        $maxGrade = (clone $baseQuery)->orderByDesc('score')->first();
        $minGrade = (clone $baseQuery)->orderBy('score')->first();

        return [
            // 📈 NILAI TERTINGGI
            Stat::make(
                '📈 Valor Máximo',
                $maxGrade ? number_format($maxGrade->score, 1) : '-'
            )
            ->description(
                $maxGrade
                    ? $this->buildDescription($maxGrade)
                    : 'Sem dados'
            )
            ->color('success'),

            // 📉 NILAI TERENDAH
            Stat::make(
                '📉 Valor Mínimo',
                $minGrade ? number_format($minGrade->score, 1) : '-'
            )
            ->description(
                $minGrade
                    ? $this->buildDescription($minGrade)
                    : 'Sem dados'
            )
            ->color('danger'),
        ];
    }

    /**
     * 🔹 Helper untuk format deskripsi
     */
    protected function buildDescription(Grade $grade): string
    {
        $student = $grade->student?->name ?? '-';
        $class   = $grade->classRoom
            ? "{$grade->classRoom->level}-{$grade->classRoom->turma}"
            : '-';
        $subject = $grade->subject?->name ?? '-';

        return "{$student} | {$class} | {$subject}";
    }
}
