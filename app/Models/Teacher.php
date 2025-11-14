<?php

namespace App\Models;

use App\Models\Subject;
use App\Models\SubjectAssignment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{
    use HasFactory;

    // Mass assignable fields sesuai migration terbaru
    protected $fillable = [
        'teacher_id',
        'name',
        'gender',
        'birth_date',
        'birth_place',
        'educational_qualification',
        'employment_status',
        'employment_start_date',
        'phone',
        'email',
        'photo',
    ];

    /**
     * Relationship: Teacher has many SubjectAssignments
     */
    public function subjectAssignments()
    {
        return $this->hasMany(SubjectAssignment::class);
    }

    /**
     * Optionally, relationship shortcut to subjects through assignments
     */
    public function subjects()
    {
        return $this->hasManyThrough(
            Subject::class,
            SubjectAssignment::class,
            'teacher_id',   // FK di subject_assignments
            'id',           // FK di subjects
            'id',           // PK di teachers
            'subject_id'    // PK di subject_assignments
        );
    }
}
