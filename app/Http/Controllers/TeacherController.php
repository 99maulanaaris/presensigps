<?php

namespace App\Http\Controllers;

use App\Imports\ImportTeacher;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class TeacherController extends Controller
{
    public function index ()
    {
        $data = Teacher::all();
        return view('guru.index',compact('data'));
    }


    public function store(Request $request)
    {
        if($request->id){
            $validasi = Validator::make($request->all(),[
                'nip' => [
                    'required',
                    Rule::unique('teachers','nip')->ignore($request->id)
                ],
                'nama_lengkap' => 'required',
                'alamat' => 'required',
                'pelajaran' => 'required'
            ]);

            if($validasi->fails()){
                return back()->with('warning',$validasi->errors()->first());
            }

            $teacher = Teacher::find($request->id);
            User::where('id',$teacher->users_id)->update([
                'name' => $request->nama_lengkap,
                'email' => $request->email
            ]);

            if($request->hasFile('foto')){
                $image = $request->file('foto');
                $imageName = $request->nip . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('foto'),$imageName);

                Teacher::where('id',$request->id)->update([
                    'nip' => $request->nip,
                    'name' => $request->nama_lengkap,
                    'alamat' => $request->alamat,
                    'nomor' => $request->no_hp,
                    'pelajaran' => $request->pelajaran,
                    'foto' => 'foto/' . $imageName
                ]);

                return back()->with('success','Data Berhasil Di Edit');
            }


            Teacher::where('id',$request->id)->update([
                'nip' => $request->nip,
                'name' => $request->nama_lengkap,
                'alamat' => $request->alamat,
                'nomor' => $request->no_hp,
                'pelajaran' => $request->pelajaran,
            ]);

            return back()->with('success','Data Berhasil Di Edit');
        }

        $validasi = Validator::make($request->all(),[
            'nip' => 'required|unique:teachers,nip',
            'nama_lengkap' => 'required',
            'alamat' => 'required',
            'pelajaran' => 'required'
        ]);

        if($validasi->fails()){
            return back()->with('warning',$validasi->errors()->first());
        }

        $user = User::create([
            'name' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => Hash::make('1234')
        ]);
        $user->assignRole('Guru');

        if($request->hasFile('foto')){
            $image = $request->file('foto');
            $imageName = $request->nip . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('foto'),$imageName);

            Teacher::create([
                'nip' => $request->nip,
                'name' => $request->nama_lengkap,
                'alamat' => $request->alamat,
                'nomor' => $request->no_hp,
                'pelajaran' => $request->pelajaran,
                'foto' => 'foto/' . $imageName,
                'users_id' => $user->id
            ]);

            return back()->with('success','Data Berhasil Di Simpan');
        }


        Teacher::create([
            'nip' => $request->nip,
            'name' => $request->nama_lengkap,
            'alamat' => $request->alamat,
            'nomor' => $request->no_hp,
            'pelajaran' => $request->pelajaran,
            'users_id' => $user->id
        ]);

        return back()->with('success','Data Berhasil Di Simpan');
    }

    public function edit($id)
    {
        $data = Teacher::where('id',$id)->with('user')->first();
        return response()->json(['status' => 200,'data' => $data]);
    }

    public function import(Request $request)
    {
        try {
            $validation = Validator::make($request->all(),[
                'file-guru' => 'required|file|mimes:xlsx,csv'
            ],[
                'file-guru.file' => 'Tipe Harus File',
                'file-guru.mimes' => 'Tipe File Harus Excel atau CSV'
            ]);

            if($validation->fails()){
                return back()->with('warning',$validation->errors()->first());
            }

            Excel::import(new ImportTeacher, $request->file('file-guru'));

            return back()->with('success','Data Berhasil Di Import');
        } catch (\Exception $e) {
            return back()->with('warning',$e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        $teacher = Teacher::find($request->id);
        User::where('id',$teacher->users_id)->delete();
        Teacher::where('id',$request->id)->delete();
        return response()->json(['status' => 200,'msg' => 'Data Berhasil Di Hapus']);
    }
}
