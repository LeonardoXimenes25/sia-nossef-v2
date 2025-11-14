<?php

namespace App\Models;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    protected $fillable = [
        'academic_year_id', 
        'name', 'order', 'start_date', 'end_date'];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
