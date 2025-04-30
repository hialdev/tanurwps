<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class AccessController extends Controller
{
    public function index(){
        $accesses = Role::orderBy('name', 'ASC')->get();
        $permissions = Permission::orderBy('name', 'ASC')->get();
        return view('access.index', compact('accesses','permissions'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);
        try {
            $role = Role::create(['name' => $request->get('name')]);
            $role->syncPermissions($request->get('permissions'));

            return redirect()->route('access.index')->with('success', 'Access '.$role->name.' berhasil ditambahkan dan diberikan akses ke '.json_encode($request->get('permissions')));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan Access, Error: '.$e->getMessage());
        }
    }

    public function update($id, Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);
        try {
            $role = Role::find($id);
            $role->name = $request->get('name');
            $role->save();
            $role->syncPermissions($request->get('permissions'));

            return redirect()->route('access.index')->with('success', 'Access '.$role->name.' berhasil diperbarui dan diberikan akses ke '.json_encode($request->get('permissions')));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui Access, Error: '.$e->getMessage());
        }
    }

    public function destroy($id){
        try {
            $role = Role::find($id);
            $role->delete();

            return redirect()->route('access.index')->with('success', 'Access berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus Access, Error: '.$e->getMessage());
        }
    }
}
