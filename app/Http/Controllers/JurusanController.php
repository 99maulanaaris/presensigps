<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JurusanController extends Controller
{
    public function index()
    {
        $jurusan = Department::all();
        return view('jurusan.index',compact('jurusan'));
    }

    public function store(Request $request)
    {
        $validasi = Validator::make($request->all(),[
            'nama_dept' => 'required',
            'kode_dept' => 'required'
        ]);

        if($validasi->fails()){
            return back()->with('warning',$validasi->errors()->first());
        }

        if($request->id){
            Department::where('id',$request->id)->update([
                'nama_dept' => $request->nama_dept,
                'kode_dept' => $request->kode_dept
            ]);

            return back()->with('success','Data Berhasil Di Edit');
        }

        Department::create([
            'nama_dept' => $request->nama_dept,
            'kode_dept' => $request->kode_dept
        ]);

        return back()->with('success','Data Berhasil Di Simpan');
    }

    public function edit($id)
    {
        $data = Department::find($id);
        return response()->json(['status' => 200,'data' => $data]);
    }

    public function delete(Request $request)
    {
        Department::where('id',$request->id)->delete();
        return response()->json(['status' => 200,'msg' => 'Data Berhasil Di Delete']);
    }
}
