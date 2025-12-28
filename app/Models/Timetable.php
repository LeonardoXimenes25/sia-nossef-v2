<?php

namespace App\Models;

use App\Models\Period;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Models\AcademicYear;
use App\Models\SubjectAssignment;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    protected $fillable = [
        'subject_assignment_id',
        'class_room_id',
        'subject_id',
        'academic_year_id',
        'period_id',
        'day', 
        'start_time', 
        'end_time'
    ];

    public function subjectAssignment()
    {
        return $this->belongsTo(SubjectAssignment::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
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
