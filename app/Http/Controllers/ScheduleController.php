<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
use Illuminate\Http\Request;
use PDF; // jangan lupa install dompdf: composer require barryvdh/laravel-dompdf

class ScheduleController extends Controller
{
    public function index()
    {
        $timetables = Timetable::with([
            'subjectAssignment.teacher',
            'subjectAssignment.subject',
            'classRoom.major',
        ])->orderBy('day')->orderBy('start_time')->get();

        $classes = $timetables
            ->pluck('classRoom.level')
            ->filter()
            ->unique()
            ->values();

        $turmas = $timetables
            ->pluck('classRoom.turma')
            ->filter()
            ->unique()
            ->values();

        $teachers = $timetables
            ->pluck('subjectAssignment.teacher.name')
            ->filter()
            ->unique()
            ->values();

        $majors = $timetables
            ->pluck('classRoom.major.name')
            ->filter()
            ->unique()
            ->values();

        return view('pages.timetable.index', compact('timetables', 'classes', 'turmas', 'teachers', 'majors'));
    }

    public function download(Request $request)
    {
        $query = Timetable::with([
            'subjectAssignment.teacher',
            'subjectAssignment.subject',
            'classRoom.major',
        ]);

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

        $timetables = $query->orderBy('day')->orderBy('start_time')->get();

        if ($timetables->isEmpty()) {
            return back()->with('error', 'La iha orariu atu download.');
        }

        $pdf = PDF::loadView('pages.timetable.download', compact('timetables'));

        return $pdf->download('Orariu_ESG_NOSSEF.pdf');
    }
}
