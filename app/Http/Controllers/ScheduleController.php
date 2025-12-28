<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
use App\Models\AcademicYear;
use App\Models\Period;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        // Ambil Academic Year & Period aktif
        $activeYear = AcademicYear::where('is_active', true)->first();
        $activePeriod = Period::where('is_active', true)
            ->where('academic_year_id', $activeYear->id)
            ->first();

        // Ambil jadwal hanya untuk guru yang aktif dan subject assignment aktif
        $timetables = Timetable::with([
                'subjectAssignment.teacher',
                'subjectAssignment.subjects',
                'classRoom.major',
                'period',
                'academicYear'
            ])
            ->where('academic_year_id', $activeYear->id)
            ->where('period_id', $activePeriod->id)
            ->whereHas('subjectAssignment', function ($query) {
                $query->where('is_active', true);
            })
            ->orderByRaw("FIELD(day, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday')")
            ->orderBy('start_time')
            ->get();

        // Ambil data filter
        $classes  = $timetables->pluck('classRoom.level')->filter()->unique()->values();
        $turmas   = $timetables->pluck('classRoom.turma')->filter()->unique()->values();
        $teachers = $timetables->pluck('subjectAssignment.teacher.name')->filter()->unique()->values();
        $majors   = $timetables->pluck('classRoom.major.name')->filter()->unique()->values();
        $periods       = [$activePeriod]; // hanya period aktif
        $academicYears = [$activeYear];   // hanya academic year aktif

        return view('pages.timetable.index', compact(
            'timetables',
            'classes',
            'turmas',
            'teachers',
            'majors',
            'periods',
            'academicYears',
            'activePeriod'
        ));
    }

    public function download(Request $request)
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $activePeriod = Period::where('is_active', true)
            ->where('academic_year_id', $activeYear->id)
            ->first();

        $query = Timetable::with([
                'subjectAssignment.teacher',
                'subjectAssignment.subjects',
                'classRoom.major',
                'period',
                'academicYear'
            ])
            ->where('academic_year_id', $activeYear->id)
            ->where('period_id', $activePeriod->id)
            ->whereHas('subjectAssignment', function ($q) {
                $q->where('is_active', true);
            });

        // Filter tambahan jika ada request
        if ($request->class && $request->class !== 'all') {
            $query->whereHas('classRoom', fn($q) => $q->where('level', $request->class));
        }
        if ($request->turma && $request->turma !== 'all') {
            $query->whereHas('classRoom', fn($q) => $q->where('turma', $request->turma));
        }
        if ($request->major && $request->major !== 'all') {
            $query->whereHas('classRoom.major', fn($q) => $q->where('name', $request->major));
        }
        if ($request->teacher && $request->teacher !== 'all') {
            $query->whereHas('subjectAssignment.teacher', fn($q) => $q->where('name', $request->teacher));
        }
        if ($request->day && $request->day !== 'all') {
            $query->where('day', $request->day);
        }

        $timetables = $query->orderByRaw("FIELD(day, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday')")
                            ->orderBy('start_time')
                            ->get();

        if ($timetables->isEmpty()) {
            return back()->with('error', 'La iha orariu atu download.');
        }

        $pdf = \PDF::loadView('pages.timetable.download', compact('timetables'));
        return $pdf->download('Orariu_ESG_NOSSEF.pdf');
    }
}
