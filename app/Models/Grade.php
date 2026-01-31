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

    // ===== RELATIONS =====
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    // ===== REMARK FUNCTION =====
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

    // ===== AVERAGE SCORE PER STUDENT =====
    public function getAverageScoreAttribute(): float
    {
        return self::where('student_id', $this->student_id)
            ->where('academic_year_id', $this->academic_year_id)
            ->where('period_id', $this->period_id)
            ->avg('score') ?? 0;
    }

    // ===== RANK PER CLASS =====
    public function getRankClassAttribute(): int
    {
        $students = self::selectRaw('student_id, AVG(score) as avg_score')
            ->where('class_room_id', $this->class_room_id)
            ->where('academic_year_id', $this->academic_year_id)
            ->where('period_id', $this->period_id)
            ->groupBy('student_id')
            ->orderByDesc('avg_score')
            ->pluck('student_id')
            ->toArray();

        return array_search($this->student_id, $students) + 1;
    }

    // ===== RANK OVERALL (ALL CLASSES) =====
    public function getRankAllAttribute(): int
    {
        $students = self::selectRaw('student_id, AVG(score) as avg_score')
            ->where('academic_year_id', $this->academic_year_id)
            ->where('period_id', $this->period_id)
            ->groupBy('student_id')
            ->orderByDesc('avg_score')
            ->pluck('student_id')
            ->toArray();

        return array_search($this->student_id, $students) + 1;
    }
}
