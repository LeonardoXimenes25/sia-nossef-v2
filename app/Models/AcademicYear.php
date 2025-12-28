<?php

namespace App\Models;

use App\Models\Period;
use App\Models\Timetable;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $fillable = [
        'name', 
        'start_date', 
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function periods()
    {
        return $this->hasMany(Period::class);
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }

}
