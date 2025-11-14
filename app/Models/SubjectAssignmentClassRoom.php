<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectAssignmentClassRoom extends Model
{
    protected $fillable = [
        'subject_assignment_id',
        'class_room_id',
    ];
}
