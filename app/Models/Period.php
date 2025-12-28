<?php

namespace App\Models;

use App\Models\Timetable;
use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    protected $fillable = [
        'academic_year_id', 
        'name',
        'order', 
        'start_date', 
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }
}
