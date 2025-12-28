<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'class_room_id',
        'teacher_id',
        'academic_year_id',
        'period_id',
        'score',
        'remarks',
    ];

    protected $casts = [
        'score' => 'decimal:1',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke Student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Relasi ke Subject
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    // Relasi ke ClassRoom
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    // Relasi ke Teacher
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    // Relasi ke AcademicYear
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // Relasi ke Period
    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    // Function untuk remark berdasarkan score
    public static function getRemarksByScore($score): string
    {
        if ($score === null || $score === '') {
            return 'Não Avaliado';
        }

        $score = (float) $score;

        return match (true) {
            $score >= 9.5 => 'Excelente',
            $score >= 8.5 => 'Muito Bom',
            $score >= 7.0 => 'Bom',
            $score >= 6.0 => 'Suficiente',
            $score >= 5.0 => 'Insuficiente',
            $score >= 3.0 => 'Mau',
            $score >= 1.0 => 'Muito Mau',
            default       => 'Não Avaliado',
        };
    }
}
