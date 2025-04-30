<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index(){
        $user = auth()->user();
        return view('profile.index', compact('user'));
    }

    public function edit(){
        $user = auth()->user();
        $user = User::findOrFail($user->id);
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request){
        $id = auth()->user()->id;
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'nullable|string|min:8',
            'confirm_password' => 'nullable|string|same:password|min:8',
        ]);
        try {
            $user = User::find($id);
            if ($request->hasFile('image')) {
                if ($user->image && file_exists(storage_path('app/public/' . $user->image))) {
                    unlink(storage_path('app/public/' . $user->image));
                }
                $imagePath = $request->file('image')->store('users', 'public');
            }
            
            $isEmailTaken = User::where('email', $request->get('email'))->first();
            if ($request->get('email') != $user->email && $isEmailTaken != null) {
                return redirect()->back()->withInput()->with('error','Ooppss.. email already taken');
            }
            
            $user->image = $imagePath;
            $user->name = $request->get('name');
            $user->email = $request->get('email');
            $user->password = $request->get('password') ? Hash::make($request->get('password')) : $user->password;
            $user->save();

            if($request->has('role')){
                if($request->get('role') == 'developer' && auth()->user()->getRoleNames()[0] == 'developer'){
                    $user->assignRole('developer');
                }else if($request->get('role') != 'developer' && in_array(auth()->user()->getRoleNames()[0], ['admin', 'developer'])){
                    $user->assignRole($request->get('role'));
                }else{
                    return redirect()->back()->with('error', 'IP dan Lokasi anda telah kami simpan, segala bentuk tindakan illegal akan diproses sesuai hukum yang berlaku');
                }
            }

            return redirect()->route('user.index')->with('success', 'User '.$user->name.' berhasil diperbaharui, dengan role sebagai '.$user->getRoleNames()[0]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal meperbarui User, Error: '.$e->getMessage());
        }

    }
}
