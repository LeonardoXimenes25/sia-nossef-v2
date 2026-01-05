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

    public function getActiveStatusAttribute(): bool
    {
    $today = now();

    // tetap true kalau toggle manual aktif
    if ($this->is_active) {
        return true;
    }

    // otomatis aktif kalau tanggal sekarang masuk periode
    if ($this->start_date && $this->end_date) {
        return $today->between($this->start_date, $this->end_date);
    }

    return false;
    }

}
