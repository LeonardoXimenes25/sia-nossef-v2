<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\SubjectAssignment;
use Barryvdh\DomPDF\Facade\Pdf; // pastikan sudah install barryvdh/laravel-dompdf

class AttendancePrintController extends Controller
{
    public function printBlank(Request $request)
    {
        $classRoomId = $request->query('class_room_id');
        $subjectAssignmentId = $request->query('subject_assignment_id');

        if (!$classRoomId || !$subjectAssignmentId) {
            abort(404, 'Class Room atau Subject Assignment tidak ada.');
        }

        $classRoom = ClassRoom::findOrFail($classRoomId);
        $subjectAssignment = SubjectAssignment::findOrFail($subjectAssignmentId);

        $students = Student::where('class_room_id', $classRoomId)
            ->orderBy('name')
            ->get();

        $pdf = Pdf::loadView('attendances.print_blank', [
            'classRoom' => $classRoom,
            'subjectAssignment' => $subjectAssignment,
            'students' => $students,
            'date' => now()->toDateString(),
        ]);

        // langsung download
        return $pdf->download('Absensi_Kosong_' . $classRoom->level . $classRoom->turma . '.pdf');
    }
}
