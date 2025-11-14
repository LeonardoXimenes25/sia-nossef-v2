<?php

namespace App\Models;

use App\Models\Major;
use App\Models\Student;
use App\Models\SubjectAssignment;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    protected $fillable = [
        'level', 
        'major_id', 
        'turma'
    ];
    
    public $timestamps = false;

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function subjectAssignments()
    {
        return $this->belongsToMany(SubjectAssignment::class, 'subject_assignment_class_rooms')
                    ->withTimestamps();
    }

    public function getNameAttribute()
    {
        return $this->level . ' ' . $this->turma . ' (' . $this->major->name . ')';
    }

}
