<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{

    protected $table = 'presensi';
    use HasFactory;

    public function student ()
    {
        return $this->hasOne(Student::class,'nis','nik');
    }

    public function jam ()
    {
        return $this->hasOne(JamKerja::class,'kode_jam_kerja','kode_jam_kerja');
    }
}
