<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Models\Timetable;
use App\Models\AcademicYear;
use App\Models\Period;

class SubjectAssignment extends Model
{
    protected $fillable = [
        'teacher_id',
        'academic_year_id',
        'period_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Guru
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    // Banyak mata pelajaran
    public function subjects()
{
    return $this->belongsToMany(
        Subject::class,
        'subject_subject_assignment', // pivot table
        'subject_assignment_id',
        'subject_id'
    )->withTimestamps();
}

public function classRooms()
{
    return $this->belongsToMany(
        ClassRoom::class,
        'subject_assignment_class_rooms', // pivot table
        'subject_assignment_id',
        'class_room_id'
    )->withTimestamps();
}

    // Tahun akademik
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // Period
    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    // Jadwal
    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }
}
