<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    // Kolom yang bisa diisi massal
    protected $fillable = [
        'student_id',
        'class_room_id',
        'teacher_id',
        'subject_assignment_id',
        'academic_year_id',
        'period_id',
        'score',
        'remarks',
    ];

    protected $casts = [
        'score' => 'decimal:1', // untuk handle nilai decimal seperti 8.5
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke Murid
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Relasi ke Kelas
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    // Relasi ke Guru
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    // Relasi ke SubjectAssignment (Guru + Mata Pelajaran + Kelas)
    public function subjectAssignment()
    {
        return $this->belongsTo(SubjectAssignment::class);
    }

    // Relasi ke Tahun Ajaran
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // Relasi ke Periode
    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    /**
     * Akses cepat ke Mata Pelajaran melalui SubjectAssignment
     */
    public function subject()
    {
        return $this->subjectAssignment?->subject;
    }
}
