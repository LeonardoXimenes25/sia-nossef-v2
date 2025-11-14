<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
{
    $query = Student::with(['classRoom', 'major']);

    // Filter search
    if ($request->filled('search')) {
        $query->where(function($q) use ($request) {
            $q->where('name', 'like', '%'.$request->search.'%')
              ->orWhere('nre', 'like', '%'.$request->search.'%');
        });
    }

    if ($request->filled('class')) {
        $query->whereHas('classRoom', function($q) use ($request) {
            $q->where('level', $request->class);
        });
    }

    if ($request->filled('major')) {
    $query->whereHas('major', function($q) use($request) {
        $q->where('name', 'like', '%' . $request->major . '%');
    });
}


    if ($request->filled('gender')) {
        $query->where('sex', $request->gender);
    }

    // Ambil semua students sebelum paginate untuk stats
    $allStudents = (clone $query)->get();

    $totalStudents = $allStudents->count();
    $maleStudents = $allStudents->where('sex','m')->count();
    $femaleStudents = $allStudents->where('sex','f')->count();
    $classes = $allStudents->pluck('classRoom.level')->unique()->count();

    $students = $query->paginate(10)->withQueryString();
    $academicYear = AcademicYear::latest()->first();

    return view('pages.students.index', compact(
        'students','totalStudents','maleStudents','femaleStudents','classes','academicYear'
    ));
}

}
