<?php

namespace App\Models;

use App\Models\Subject;
use App\Models\ClassRoom;
use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    protected $fillable = [
        'code',
        'name',
    ];
    
    public $timestamps = false;

    public function subjects()
    {
    return $this->belongsToMany(Subject::class, 'subject_majors');
    }


    public function classRooms()
    {
        return $this->hasMany(ClassRoom::class);
    }
}
