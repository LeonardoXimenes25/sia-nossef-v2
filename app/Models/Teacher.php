<?php

namespace App\Models;

use App\Models\User;
use App\Models\Subject;
use App\Models\TeacherPosition;
use App\Models\SubjectAssignment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{
    use HasFactory;

    // Mass assignable fields sesuai migration terbaru
    protected $fillable = [
        'user_id',
        'teacher_position_id',
        'nrp',
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
     * Relationship to subjects through SubjectAssignment and pivot table
     */
    public function subjects()
    {
        return $this->hasManyThrough(
            Subject::class,
            SubjectAssignment::class,
            'teacher_id',                        // FK in subject_assignments pointing to teachers
            'id',                                // PK in subjects
            'id',                                // PK in teachers
        )
        ->distinct()
        ->select('subjects.*');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teacherPosition()
    {
        return $this->belongsTo(TeacherPosition::class);
    }

}
