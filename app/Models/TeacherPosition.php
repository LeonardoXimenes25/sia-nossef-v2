<?php

namespace App\Models;

use App\Models\Teacher;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class TeacherPosition extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'level',
    ];

    public function teacher(){
        return $this->hasMany(Teacher::class);
    }
}
