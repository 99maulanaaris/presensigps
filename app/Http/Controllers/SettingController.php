<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function index ()
    {
        $data = User::where('id',auth()->user()->id)->first();
        return view('setting.index',compact('data'));
    }

    public function store(Request $request)
    {

        $data = auth()->user()->roles->pluck('name');

        if($data[0] === 'Admin'){
            $user = User::where('id',auth()->user()->id)->first();
            if($request->password){
                User::where('id',auth()->user()->id)->update([
                    'name' => $request->name ?? $user->name,
                    'email' => $request->email ?? $user->email,
                    'password' => Hash::make($request->password)
                ]);
                return back()->with('success','Data Berhasil Di Update');
            }
            User::where('id',auth()->user()->id)->update([
                'name' => $request->name ?? $user->name,
                'email' => $request->email ?? $user->email,
            ]);

            return back()->with('success','Data Berhasil Di Update');
        }

        $user = User::where('id',auth()->user()->id)->first();
        if($request->password){
            User::where('id',auth()->user()->id)->update([
                'name' => $request->name ?? $user->name,
                'email' => $request->email ?? $user->email,
                'password' => Hash::make($request->password)
            ]);

            $teacher = Teacher::where('users_id',auth()->user()->id)->first();

            Teacher::where('users_id',auth()->user()->id)->update([
                'name' => $request->name ?? $teacher->name
            ]);


            return back()->with('success','Data Berhasil Di Update');
        }
        User::where('id',auth()->user()->id)->update([
            'name' => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
        ]);

        $teacher = Teacher::where('users_id',auth()->user()->id)->first();

        Teacher::where('users_id',auth()->user()->id)->update([
            'name' => $request->name ?? $teacher->name
        ]);

        return back()->with('success','Data Berhasil Di Update');


    }
}
