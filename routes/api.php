<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\StageApprovalController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\WorkspaceTaskController;
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

Route::prefix('approval')->group(function () {
    Route::post('/fetch', [ApprovalController::class, 'getApprovals']);
    Route::post('/check', [ApprovalController::class, 'getApprovalBoolean']);
});

Route::middleware('api.agent.access')->group(function () {
   Route::get('/histories', [HistoryController::class, 'index']);

   // Workspace Management
   Route::prefix('workspaces')->group(function () {
      Route::get('/', [WorkspaceController::class, 'index']);
      Route::get('/list', [WorkspaceController::class, 'list']);
      Route::get('/add', [WorkspaceController::class, 'add']);
      Route::post('/', [WorkspaceController::class, 'store']);
      Route::get('/{workspace_id}', [WorkspaceController::class, 'show']);
      Route::get('/{workspace_id}/edit', [WorkspaceController::class, 'edit']);
      Route::put('/{workspace_id}', [WorkspaceController::class, 'update']);
      Route::delete('/{workspace_id}', [WorkspaceController::class, 'destroy']);
      Route::post('/{workspace_id}/stages/{stage_id}/send-approval', [StageApprovalController::class, 'send']);
      
      Route::prefix('tasks')->group(function () {
         Route::get('/{task_id}', [WorkspaceTaskController::class, 'show']);
         Route::post('/{task_id}', [WorkspaceTaskController::class, 'store']);
         Route::put('/{task_id}/workspace_task/{wtask_id}', [WorkspaceTaskController::class, 'update']);
      });
   });

   // Workspace Approvals
   Route::prefix('approvals')->group(function () {
      Route::get('/', [ApprovalController::class, 'index']);
      Route::get('/{approval_id}', [ApprovalController::class, 'show']);
      Route::post('/{approval_id}/decision', [ApprovalController::class, 'decision']);
      Route::put('/{approval_id}/decision', [ApprovalController::class, 'updateDecision']);
   });

   // Stage Approvals
   Route::prefix('stage-approvals')->group(function () {
      Route::get('/', [StageApprovalController::class, 'index']);
      Route::get('/{approval_id}', [StageApprovalController::class, 'show']);
      Route::post('/{approval_id}/decision', [StageApprovalController::class, 'decision']);
      Route::put('/{approval_id}/decision', [StageApprovalController::class, 'updateDecision']);
   });

});


