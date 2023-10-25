<?php

namespace App\Http\Controllers;

use App\Imports\ImportStudent;
use App\Models\Department;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $students = Student::all();
        $kelas = StudentClass::all();
        $departemen = Department::all();
        return view('student.index',compact('departemen','kelas','students'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->id){
            $validasi = Validator::make($request->all(),[
                'nis' => [
                    'required',
                    Rule::unique('students','nis')->ignore($request->id)
                ],
                'nama_lengkap' => 'required',
                'alamat' => 'required',
                'kelas' => 'required',
                'jurusan' => 'required'
            ]);

            if($validasi->fails()){
                return back()->with('warning',$validasi->errors()->first());
            }
            $student = Student::find($request->id);
            if($request->hasFile('foto')){
                if(File::exists(public_path($student->foto))){
                    File::delete(public_path($student->foto));
                }
                $image = $request->file('foto');
                $imageName = $request->nis . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('foto-siswa'),$imageName);

                Student::where('id',$request->id)->update([
                    'nis' => $request->nis,
                    'nama' => $request->nama_lengkap,
                    'foto' => 'foto-siswa/' . $imageName,
                    'kelas_id' => $request->kelas,
                    'jurusan_id' => $request->jurusan,
                    'nomor' => $request->no_hp,
                    'alamat' => $request->alamat
                ]);
                return back()->with('success','Data Berhasil Di Edit');
            }

            Student::where('id',$request->id)->update([
                'nis' => $request->nis,
                'nama' => $request->nama_lengkap,
                'kelas_id' => $request->kelas,
                'jurusan_id' => $request->jurusan,
                'nomor' => $request->no_hp,
                'alamat' => $request->alamat
            ]);

            return back()->with('success','Data Berhasil Di Edit');

        }
        $validasi = Validator::make($request->all(),[
            'nis' => 'required|unique:students,nis',
            'nama_lengkap' => 'required',
            'alamat' => 'required',
            'kelas' => 'required',
            'jurusan' => 'required'
        ]);

        if($validasi->fails()){
            return back()->with('warning',$validasi->errors()->first());
        }
        if($request->hasFile('foto')){
            $image = $request->file('foto');
            $imageName = $request->nis . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('foto-siswa'),$imageName);

            Student::create([
                'nis' => $request->nis,
                'nama' => $request->nama_lengkap,
                'kelas_id' => $request->kelas,
                'jurusan_id' => $request->jurusan,
                'nomor' => $request->no_hp,
                'alamat' => $request->alamat,
                'foto' => 'foto-siswa/' . $imageName,
                'password' => Hash::make('1234')
            ]);

            return back()->with('success','Data Berhasil Di Simpan');
        }


        Student::create([
            'nis' => $request->nis,
            'nama' => $request->nama_lengkap,
            'kelas_id' => $request->kelas,
            'jurusan_id' => $request->jurusan,
            'nomor' => $request->no_hp,
            'alamat' => $request->alamat,
            'password' => Hash::make('1234')
        ]);

        return back()->with('success','Data Berhasil Di Simpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Student::where('id',$id)->with('kelas','jurusan')->first();
        return response()->json(['status' => 200,'data' => $data]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $data = Student::find($request->id);
        File::delete(public_path($data->foto));
        Student::where('id',$request->id)->delete();
        return response()->json(['status' => 200 , 'msg' => 'Data Berhasil Di Delete']);
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

            Excel::import(new ImportStudent, $request->file('file-guru'));

            return back()->with('success','Data Berhasil Di Import');
        } catch (\Exception $e) {
            return back()->with('warning',$e->getMessage());
        }
    }
}
