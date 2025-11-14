<?php

namespace App\Models;

use App\Models\Grade;
use App\Models\Major;
use App\Models\ClassRoom;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    // Mass assignable attributes
    protected $fillable = [
        'nre',
        'name',
        'sex',
        'class_room_id',
        'major_id',
        'birth_date',
        'birth_place',
        'address',
        'province',
        'district',
        'subdistrict',
        'parent_name',
        'parent_contact',
        'admission_year',
        'status',
        'photo',
        'email',
    ];

    // Relasi ke kelas
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    // Relasi ke jurusan
    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    // Relasi ke nilai / grades
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    // Accessor untuk menampilkan jenis kelamin full text
    public function getSexTextAttribute()
    {
        return $this->sex === 'm' ? 'Mane' : 'Feto';
    }

    // Accessor untuk menampilkan status full text
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'active' => 'Ativu',
            'alumni' => 'Alumni',
            'left'   => 'Sai',
        };
    }

    // Accessor untuk menampilkan foto default jika kosong
    public function getPhotoUrlAttribute()
    {
        return $this->photo 
            ? asset('storage/' . $this->photo) 
            : asset('assets/img/default-avatar.png');
    }
    
    // Relasi
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Ambil status absensi untuk tanggal tertentu
    public function attendanceFor($date)
    {
        return $this->attendances()->where('date', $date)->first();
    }


}
