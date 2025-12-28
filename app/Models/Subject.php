<?php

namespace App\Models;

use App\Models\Major;
use App\Models\SubjectAssignment;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name',
    ];

    public $timestamps = false;

    public function majors()
    {
        return $this->belongsToMany(Major::class, 'subject_majors');
    }

    public function subjectAssignments()
    {
        return $this->belongsToMany(SubjectAssignment::class, 'subject_subject_assignment');
    }
}