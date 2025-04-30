<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(){
        $user = auth()->user();
        $users = [];
        if($user->getRoleNames()[0] == 'developer'){
            $users = User::where('id', '!=', $user->id)->get();
        }else if($user->getRoleNames()[0] == 'admin'){
            $users = User::withoutRole(['developer'])->get();
        }else{
            return redirect()->route('home')->with('error', 'Anda dialihkan karena tidak ada akses ke halaman sebelumnya.');
        }
        return view('users.index', compact('users'));
    }

    public function add(){
        $roles = Role::orderBy('name', 'ASC')->get()->reject(fn($name) => $name === 'developer');
        if(User::find(auth()->user()->id)->getRoleNames()[0] != 'developer')
            $roles = Role::orderBy('name', 'ASC')->get();
        return view('users.add', compact('roles'));
    }

    public function store(Request $request){
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|numeric|digits_between:6,15',
            'password' => 'required|string|min:8',
            'confirm_password' => 'required|string|same:password|min:8',
            'role' => 'required|string|exists:roles,name',
        ]);
        try {
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('users', 'public');
            } else {
                return redirect()->back()->with('error', 'Image is required'); 
            }
            $user = new User();
            $user->image = $imagePath;
            $user->name = $request->get('name');
            $user->email = $request->get('email');
            $user->phone = $request->get('phone');
            $user->password = Hash::make($request->get('password'));
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

            return redirect()->route('user.index')->with('success', 'User '.$user->name.' berhasil ditambahkan, dengan role sebagai '.$user->getRoleNames()[0]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan User, Error: '.$e->getMessage());
        }
    }

    public function edit($id){
        $user = User::findOrFail($id);
        $roles = Role::orderBy('name', 'ASC')->get()->reject(fn($name) => $name === 'developer');
        return view('users.edit', compact('user', 'roles'));
    }

    public function update($id, Request $request){
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'nullable|numeric|digits_between:6,15',
            'password' => 'nullable|string|min:8',
            'confirm_password' => 'nullable|string|same:password|min:8',
            'role' => 'nullable|string|exists:roles,name',
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
            
            $user->image = $imagePath ?? $user->image;
            $user->position = $request->get('position');
            $user->name = $request->get('name');
            $user->position = $request->get('position');
            $user->email = $request->get('email');
            $user->phone = $request->get('phone');
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

    public function destroy($id){
        try {
            $user = User::find($id);
            
            $user->delete();

            return redirect()->route('user.index')->with('success', 'User berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus User, Error: '.$e->getMessage());
        }
    }
}
