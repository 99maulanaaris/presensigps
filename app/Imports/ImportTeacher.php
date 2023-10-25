<?php

namespace App\Imports;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ImportTeacher implements ToCollection,WithStartRow
{

    public function startRow(): int
    {
        return 2;
    }


    public function collection(Collection $collection)
    {
        foreach ($collection as $item) {
            if(!empty($item[0])){
                $user = User::create([
                    'name' => $item[1],
                    'email' => $item[6],
                    'password' => Hash::make('1234')
                ]);

                $user->assignRole('Guru');
                $phone = $item[3];
                Teacher::create([
                    'nip' => $item[0],
                    'name' => $item[1],
                    'alamat' => $item[4],
                    'nomor' => '0' . substr($phone, 2),
                    'pelajaran' => $item[2],
                    'users_id' => $user->id
                ]);
            }
        }
    }
}
