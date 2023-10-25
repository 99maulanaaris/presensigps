<?php

namespace App\Imports;

use App\Models\Department;
use App\Models\Student;
use App\Models\StudentClass;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ImportStudent implements ToCollection, WithStartRow
{
    public function startRow(): int
    {
        return 2;
    }
    public function collection(Collection $collection)
    {
        foreach ($collection as $item) {
            if(!empty($item[0])){
                $kelas = StudentClass::where('kode_kelas',$item[3])->first();
                $jurusan = Department::where('nama_dept',$item[2])->first();
                Student::create([
                    'nis' => $item[0],
                    'nama' => $item[1],
                    'kelas_id' => $kelas ? $kelas->id : null,
                    'jurusan_id' => $jurusan ? $jurusan->id : null,
                    'nomor' => $item[4],
                    'alamat' => $item[5],
                    'password' => Hash::make('1234')
                ]);
            }
        }
    }
}
