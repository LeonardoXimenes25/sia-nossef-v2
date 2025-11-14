<?php

namespace App\Models;

use App\Models\Major;
use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    protected $fillable = [
        'name',
        'major_id',
        'grade_level',
        'academic_year_id',
    ];

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function subjectAssignments()
    {
        return $this->hasMany(SubjectAssignment::class, 'class_id');
    }
}
