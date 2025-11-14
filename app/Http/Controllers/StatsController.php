<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassRoom;

class StatsController extends Controller
{
    public function index()
    {
        // Hitung total
        $totalTeachers = Teacher::count();
        $totalStudents = Student::count();
        $totalSubjects = Subject::count();
        $totalClasses  = ClassRoom::count();

        // Untuk chart: ambil jumlah siswa per kelas
        $classRooms = ClassRoom::with('students')->get();
        $classNames = $classRooms->pluck('level')->toArray();
        $studentsPerClass = $classRooms->map(fn($c) => $c->students->count())->toArray();

        return view('pages.home.hero', compact(
            'totalTeachers',
            'totalStudents',
            'totalSubjects',
            'totalClasses',
            'classNames',
            'studentsPerClass'
        ));
    }
}
