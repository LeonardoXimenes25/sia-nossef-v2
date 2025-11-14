<?php

namespace App\Models;

use App\Models\Period;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ClassRoom;
use App\Models\AcademicYear;
use App\Models\SubjectAssignment;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'student_id',
        'class_room_id',
        'teacher_id',
        'subject_assignment_id',
        'academic_year_id',
        'period_id',
        'date',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subjectAssignment()
    {
        return $this->belongsTo(SubjectAssignment::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
