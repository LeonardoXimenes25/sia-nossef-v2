<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with('teacherPosition')
            ->whereNotNull('teacher_position_id')
            ->orderBy('teacher_position_id')
            ->get();

        return view('pages.about', compact('teachers'));
    }
}
