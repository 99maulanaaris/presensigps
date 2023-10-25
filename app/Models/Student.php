<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    protected $guarded = [];
    use HasFactory;

    public function kelas()
    {
        return $this->hasOne(StudentClass::class,'id','kelas_id');
    }

    public function jurusan()
    {
        return $this->hasOne(Department::class,'id','jurusan_id');
    }
}
