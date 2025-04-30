<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('api.token')->post('/upload', function (Request $request) {
    if (!$request->hasFile('file')) {
        return response()->json(['error' => 'No file uploaded'], 400);
    }
    $folder = $request->get('folder');
    $file = $request->file('file');
    $originalName = $file->getClientOriginalName();
    $filename = $folder.'/' . $originalName;

    // Jika file sudah ada, tambahkan suffix unik
    if (Storage::disk('public')->exists($filename)) {
        $extension = $file->getClientOriginalExtension();
        $basename = pathinfo($originalName, PATHINFO_FILENAME);
        $filename = $folder.'/' . $basename . '-' . Str::random(5) . '.' . $extension;
    }

    // Simpan file
    $path = $file->storeAs($folder, basename($filename), 'public');

    return response()->json(['path' => $path, 'url' => Storage::url($path)], 201);
});

// Route untuk delete file
Route::middleware('api.token')->post('/delete', function (Request $request) {
    $filePath = $request->get('path') ?? null;

    // Cek apakah file ada di disk
    if ($filePath && Storage::disk('public')->exists($filePath)) {
        // Hapus file dari disk
        Storage::disk('public')->delete($filePath);
        return response()->json(['message' => 'File deleted successfully.'], 200);
    }

    return response()->json(['error' => 'File not found'], 404);
});
