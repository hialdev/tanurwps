<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index(){
        $settings = Setting::all();
        $uniqueGroups = $settings->map(function ($item) {
            return (object)[
                'group' => $item->group,
                'group_key' => $item->group_key,
            ];
        })->unique();
        $groups = $settings->groupBy('group_key');
        return view('settings.index', compact('settings', 'groups', 'uniqueGroups'));
    }

    public function store(Request $request){
        $request->validate([
            'group' => 'nullable|string|exists:settings,group_key',
            'group_name' => 'nullable|string',
            'group_key' => 'nullable|string',
            'name' => 'required|string',
            'key' => 'required|string|unique:settings,key',
            'description' => 'nullable|string',
            'input_type' => 'required|string|in:'.implode(',', array_keys(config('al.input_type'))),
            'is_urgent' => 'nullable|boolean',
        ]);
        try {
            $setting = new Setting();
            $setting->group = $request->get('group') ? Setting::where('group_key',$request->get('group'))->first()->group : $request->get('group_name');
            $setting->group_key = $request->get('group') ?? $request->get('group_key');
            $setting->name = $request->get('name');
            $setting->key = $request->get('key');
            $setting->description = $request->get('description');
            $setting->input_type = $request->get('input_type');
            $setting->is_urgent = $request->get('is_urgent') ?? 0;
            $setting->save();

            return redirect()->route('setting.index')->with('success', 'Pengaturan '.$setting->name.' berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pengaturan, Error: '.$e->getMessage());
        }
    }

    public function destroy($id){
        try {
            $setting = Setting::findOrFail($id);
            if($setting->is_urgent) return redirect()->back()->withInput()->with('error', 'Tidak dapat dihapus, Setting ini termasuk urgent!');
            $this->checkFile($setting);
            $setting->delete();

            return redirect()->route('setting.index')->with('success', 'Pengaturan '.$setting->name.' berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menghapus pengaturan, Error: '.$e->getMessage());
        }
    }

    public function update($id, Request $request){
        $validation = '';
        switch ($request->get('input_type')) {
            case 'text':
                $validation = 'string';
                break;
            case 'number':
                $validation = 'numeric';
                break;
            case 'date':
                $validation = 'date';
                break;
            case 'textarea':
                $validation = 'string';
                break;
            // case 'select':
            //     $validation = 'string|in:option1,option2,option3'; // sesuaikan dengan pilihan select yang valid
            //     break;
            case 'toggle':
                $validation = 'boolean';
                break;
            case 'image':
                $validation = 'image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048'; // untuk mendukung semua format image yang disebutkan
                break;
            case 'multiple_image':
                $validation = 'array|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048'; // jika ingin multiple image
                break;
            case 'file':
                $validation = 'file|mimes:pdf,ppt,doc,docx,xlsx,csv,txt,pptx,odt|max:10240'; // untuk berbagai jenis file dokumen
                break;
            case 'multiple_file':
                $validation = 'array|file|mimes:pdf,ppt,doc,docx,xlsx,csv,txt,pptx,odt|max:10240'; // untuk multiple file
                break;
            default:
                $validation = 'string';
                break;
        }

        $validate = [
            $request->get('key') => 'required|'.$validation,
        ];
        $request->validate([$validate]);

        try {
            $key = $request->get('key');
            $setting = Setting::findOrFail($id);
            $isFile = $request->get('input_type') == 'file' || $request->get('input_type') == 'image' || $request->get('input_type') == 'multiple_file' || $request->get('input_type') == 'multiple_image';
            $setting->value = $isFile ? $this->doUpload($request->file($key), $setting->value, is_array($key)) : $request->get($key);
            $setting->save();

            return redirect()->route('setting.index')->with('success', $key.' Setting berhasil di perbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menetapkan nilai setting, Error: '.$e->getMessage());
        }

    }

    private function doUpload($file = null, $oldPath = null, $multiple = false)
    {
        // Menghapus file lama jika ada dan ada path lama
        if ($oldPath && file_exists(storage_path('app/public/' . $oldPath))) {
            Storage::disk('public')->delete($oldPath);  // Hapus file lama
        }

        // Mengecek apakah file baru diunggah
        if ($file) {
            // Jika file adalah array (multiple file)
            if ($multiple && is_array($file)) {
                $paths = [];  // Array untuk menyimpan path file yang diunggah

                // Loop untuk meng-upload setiap file dalam array
                foreach ($file as $item) {
                    // Mengecek apakah file valid
                    if ($item->isValid()) {
                        $path = $item->store('settings', 'public');
                        $paths[] = $path;  // Menyimpan path file yang diunggah dalam array
                    }
                }

                // Mengembalikan array paths untuk multiple file
                return response()->json($paths);

            } else {
                // Jika hanya satu file
                if ($file->isValid()) {
                    $path = $file->store('settings', 'public');
                    return $path;  // Mengembalikan path file yang diunggah
                }
            }
        }

        // Jika tidak ada file yang diunggah, kembalikan oldPath (path file lama)
        return $oldPath ? $oldPath : null;  // Mengembalikan file lama jika ada
    }

    public function clear($id){
        try {
            $setting = Setting::findOrFail($id);
            $isFileSingle = $setting->input_type == 'file' || $setting->input_type == 'image';
            $isFileMultiple = $setting->input_type == 'multiple_file' || $setting->input_type == 'multiple_image';
            
            $value = $isFileMultiple ? json_decode($setting->value) : $setting->value;
            if ($isFileSingle){
                if(file_exists(storage_path('app/public/' . $value))) {
                    Storage::disk('public')->delete($value);  // Hapus file lama
                }
            }else if($isFileMultiple){
                foreach ($value as $fl) {
                    if(file_exists(storage_path('app/public/' . $fl))) {
                        Storage::disk('public')->delete($fl);  // Hapus file lama
                    }
                }
            }

            $setting->value = null;
            $setting->save();

            return redirect()->route('setting.index')->with('success', 'Penghapusan nilai setting '.$setting->name.' berhasil dilakukan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengosongkan nilai, Error: '.$e->getMessage());
        }

    }

    public function force($id){
        try {
            $setting = Setting::findOrFail($id);
            $this->checkFile($setting);
            $setting->delete();

            return redirect()->route('setting.index')->with('success', 'Penghapusan pengaturan '.$setting->name.' berhasil dilakukan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus pengaturan, Error: '.$e->getMessage());
        }
    }
    
    private function checkFile($setting){
        $isFileSingle = $setting->input_type == 'file' || $setting->input_type == 'image';
        $isFileMultiple = $setting->input_type == 'multiple_file' || $setting->input_type == 'multiple_image';
        
        $value = $isFileMultiple ? json_decode($setting->value) : $setting->value;
        if ($isFileSingle && $value){
            if(file_exists(storage_path('app/public/' . $value))) {
                Storage::disk('public')->delete($value);  // Hapus file lama
            }
        }else if($isFileMultiple && $value){
            foreach ($value as $fl) {
                if(file_exists(storage_path('app/public/' . $fl))) {
                    Storage::disk('public')->delete($fl);  // Hapus file lama
                }
            }
        }

        return true;
    }
}
