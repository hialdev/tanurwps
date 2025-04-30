<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class ApplicationController extends Controller
{
    public function index(){
        $applications = Application::orderBy('name', 'ASC')->get();
        return view('applications.index', compact('applications'));
    }

    public function add(){
        return view('applications.add');
    }

    public function store(Request $request){
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:applications,code',
            'icon' => 'nullable|string|max:255',
            'url' => 'required|url',
        ]);
        try {

            $application = new Application();
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('applications', 'public');
                $application->image = $imagePath;
            }
            $application->name = $request->get('name');
            $application->code = $request->get('code');
            $application->icon = $request->get('icon');
            $application->url = $request->get('url');
            $application->save();
            
            Permission::create(['name' => $request->get('name')]);

            return redirect()->route('application.index')->with('success', 'Application '.$application->name.' berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->withInput()->back()->with('error', 'Gagal menambahkan Application, Error: '.$e->getMessage());
        }
    }

    public function edit($id){
        $application = Application::findOrFail($id);
        return view('applications.edit', compact('application'));
    }

    public function update($id, Request $request){
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:applications,code,'.$id,
            'icon' => 'nullable|string|max:255',
            'url' => 'required|url',
        ]);
        try {
            $application = Application::find($id);
            if ($request->hasFile('image')) {
                if ($application->image && file_exists(storage_path('app/public/' . $application->image))) {
                    unlink(storage_path('app/public/' . $application->image));
                }
                $imagePath = $request->file('image')->store('applications', 'public');
                $application->image = $imagePath;
            }

            $permission = Permission::where('name', $application->name)->first();
            if($permission){
                $permission->name = $request->get('name');
                $permission->update();
            }

            $application->name = $request->get('name');
            $application->code = $request->get('code');
            $application->icon = $request->get('icon');
            $application->url = $request->get('url');
            $application->save();



            return redirect()->route('application.index')->with('success', 'Application '.$application->name.' berhasil diperbaharui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui Application, Error: '.$e->getMessage());
        }
    }

    public function destroy($id){
        try {
            $application = Application::find($id);
            $permission = Permission::where('name', $application->name)->first();
            if ($permission) $permission->delete();
            $application->delete();

            return redirect()->route('application.index')->with('success', 'Application berhasil dihapus dengan permissionnya');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus Application, Error: '.$e->getMessage());
        }
    }
}
