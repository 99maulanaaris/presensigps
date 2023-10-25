<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuanizin extends Model
{
    use HasFactory;
    protected $table = 'pengajuan_izin';

    public function student ()
    {
        return $this->hasOne(Student::class,'nis','nik');
    }
}
