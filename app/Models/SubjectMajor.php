<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectMajor extends Model
{
    protected $table = 'subject_major'; // nama tabel pivot
    public $timestamps = false; // pivot biasanya tidak perlu timestamp
}
