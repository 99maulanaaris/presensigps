<?php

namespace App\Http\Controllers;

use App\Models\StudentClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SiswaKelasController extends Controller
{
    public function index()
    {
        $data = StudentClass::all();
        return view('kelas.index',compact('data'));
    }

    public function store(Request $request)
    {
        $validasi = Validator::make($request->all(),[
            'kode_kelas' => 'required',
            'nama_kelas' => 'required'
        ]);

        if($validasi->fails()){
            return back()->with('warning',$validasi->errors()->first());
        }

        if($request->id){
            StudentClass::where('id',$request->id)->update([
                'kode_kelas' => $request->kode_kelas,
                'nama' => $request->nama_kelas
            ]);

            return back()->with('success','Data Berhasil Di Edit');
        }
        StudentClass::create([
            'kode_kelas' => $request->kode_kelas,
            'nama' => $request->nama_kelas
        ]);

        return back()->with('success','Data Berhasil Di Simpan');
    }

    public function edit($id)
    {
        $data = StudentClass::find($id);
        if($data) {
            return response()->json(['status' => 200,'data' => $data]);
        }
        return response()->json(['status' => 200,'msg' => 'Data Tidak Di Temukan']);
    }

    public function delete (Request $request)
    {
        StudentClass::where('id',$request->id)->delete();
        return response()->json(['status' => 200,'msg' => 'Data Berhasil Di Hapus'],200);
    }
}
