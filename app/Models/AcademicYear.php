<?php

namespace App\Models;

use App\Models\Period;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $fillable = [
        'name', 
        'start_date', 
        'end_date'
    ];

    public function periods()
    {
        return $this->hasMany(Period::class);
    }
}
