<?php

namespace App\Models;

use App\Models\ClassRoom;
use App\Models\SubjectAssignment;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    protected $fillable = [
        'subject_assignment_id',
        'class_room_id',
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
}
