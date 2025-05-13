<?php

use App\Http\Controllers\AccessController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StageApprovalController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\WorkspaceTaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function(){
//     return redirect()->route('home');
// });

Route::get('/app.css', function () {
    $theme = config('al.theme'); // Memuat konfigurasi tema
    return response()
        ->view('styles.app', ['theme' => $theme])
        ->header('Content-Type', 'text/css');
});

// -------------- Auth Routes ----------------
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/login/email', [LoginController::class, 'showLoginEmail'])->name('login.email');
Route::post('/login/email', [LoginController::class, 'withEmail'])->name('login.email');
Route::get('/login/phone', [LoginController::class, 'showLoginPhone'])->name('login.phone');
Route::post('/login/phone', [LoginController::class, 'withPhone'])->name('login.phone');
Route::get('/otp/input', [LoginController::class, 'inputOTP'])->name('otp.input');
Route::post('/otp/{type}/submit', [LoginController::class, 'submitOTP'])->name('otp.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
Route::post('/password/confirm', [ConfirmPasswordController::class, 'confirm']);
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

//Auth::routes();
// -------------- End Auth Routes ----------------

// -------------- Unauthenticated routes ------------------

Route::get('/agent', [AgentController::class, 'index'])->name('agent.index');
Route::middleware(['web', 'agent.access'])->prefix('agent')->group(function () {
    Route::get('/detail', [AgentController::class, 'detail'])->name('agent.detail');
    Route::get('/superiors', [AgentController::class, 'superiors'])->name('agent.superiors');

    Route::get('/dashboard', [WorkspaceController::class, 'index'])->name('agent.workspace.index');
    Route::get('/workspace', [WorkspaceController::class, 'list'])->name('agent.workspace.list');
    Route::get('/workspace/add', [WorkspaceController::class, 'add'])->name('agent.workspace.add');
    Route::post('/workspace/store', [WorkspaceController::class, 'store'])->name('agent.workspace.store');
    Route::get('/workspace/{workspace_id}/detail', [WorkspaceController::class, 'show'])->name('agent.workspace.show');
    Route::get('/workspace/{workspace_id}/edit', [WorkspaceController::class, 'edit'])->name('agent.workspace.edit');
    Route::put('/workspace/{workspace_id}/update', [WorkspaceController::class, 'update'])->name('agent.workspace.update');
    Route::delete('/workspace/{workspace_id}/destroy', [WorkspaceController::class, 'destroy'])->name('agent.workspace.destroy');
    
    Route::post('/workspace/{workspace_id}/stage/{stage_id}/send', [StageApprovalController::class, 'send'])->name('agent.workspace.stage.send');
    
    Route::get('/workspace/{workspace_id}/task/{task_id}', [WorkspaceTaskController::class, 'show'])->name('agent.workspace.task.show');
    Route::post('/workspace/{workspace_id}/task/{task_id}/store', [WorkspaceTaskController::class, 'store'])->name('agent.workspace.task.store');
    Route::put('/workspace/{workspace_id}/task/{task_id}/update/{wtask_id}', [WorkspaceTaskController::class, 'update'])->name('agent.workspace.task.update');


    Route::get('/history', [HistoryController::class, 'index'])->name('agent.history.index');
    
    Route::get('/approval', [ApprovalController::class, 'index'])->name('agent.approval.index');
    
    Route::get('/approval/stage/{approval_id}/detail', [StageApprovalController::class, 'show'])->name('agent.approval.stage.show');
    Route::post('/approval/stage/{approval_id}/decision', [StageApprovalController::class, 'decision'])->name('agent.approval.stage.decision');
    Route::post('/approval/stage/{approval_id}/decision/update', [StageApprovalController::class, 'updateDecision'])->name('agent.approval.stage.decision.update');
    
    Route::get('/approval/{approval_id}/detail', [ApprovalController::class, 'show'])->name('agent.approval.show');
    Route::post('/approval/{approval_id}/decision', [ApprovalController::class, 'decision'])->name('agent.approval.decision');
    Route::post('/approval/{approval_id}/decision/update', [ApprovalController::class, 'updateDecision'])->name('agent.approval.decision.update');
});

Route::middleware(['auth'])->group(function(){
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
    
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/edit', [ProfileController::class, 'update'])->name('profile.update');

    Route::middleware(['role:developer'])->group(function(){
        Route::delete('/setting/{id}/force',[SettingController::class, 'force'])->name('setting.force');
        Route::delete('/access/{id}/destroy',[AccessController::class, 'destroy'])->name('access.destroy');
    });

    Route::middleware(['role:admin|developer'])->group(function (){
        Route::get('/whatsapp', function () {
            return view('whatsapp.index');
        })->name('whatsapp.index');

        Route::get('/access',[AccessController::class, 'index'])->name('access.index');
        Route::post('/access/store',[AccessController::class, 'store'])->name('access.store');
        Route::post('/access/{id}/update',[AccessController::class, 'update'])->name('access.update');

        Route::get('/application',[ApplicationController::class, 'index'])->name('application.index');
        Route::get('/application/add',[ApplicationController::class, 'add'])->name('application.add');
        Route::post('/application/store',[ApplicationController::class, 'store'])->name('application.store');
        Route::get('/application/{id}/edit',[ApplicationController::class, 'edit'])->name('application.edit');
        Route::post('/application/{id}/update',[ApplicationController::class, 'update'])->name('application.update');
        Route::delete('/application/{id}/destroy',[ApplicationController::class, 'destroy'])->name('application.destroy');

        
        Route::delete('/setting/{id}',[SettingController::class, 'destroy'])->name('setting.destroy');
    });

    Route::get('/stage',[StageController::class, 'index'])->name('stage.index');
    Route::get('/stage/add',[StageController::class, 'add'])->name('stage.add');
    Route::post('/stage/store',[StageController::class, 'store'])->name('stage.store');
    Route::get('/stage/{id}/edit',[StageController::class, 'edit'])->name('stage.edit');
    Route::post('/stage/{id}/update',[StageController::class, 'update'])->name('stage.update');
    Route::delete('/stage/{id}/destroy',[StageController::class, 'destroy'])->name('stage.destroy');
    Route::delete('/stage/{id}/attachment/{attachment_id}/delete',[StageController::class, 'attachmentDelete'])->name('stage.attachment.delete');

    Route::get('/task',[TaskController::class, 'index'])->name('task.index');
    Route::get('/task/add',[TaskController::class, 'add'])->name('task.add');
    Route::post('/task/store',[TaskController::class, 'store'])->name('task.store');
    Route::get('/task/{id}/edit',[TaskController::class, 'edit'])->name('task.edit');
    Route::post('/task/{id}/update',[TaskController::class, 'update'])->name('task.update');
    Route::delete('/task/{id}/destroy',[TaskController::class, 'destroy'])->name('task.destroy');
    Route::delete('/task/{id}/attachment/{attachment_id}/delete',[TaskController::class, 'attachmentDelete'])->name('task.attachment.delete');

    Route::get('/user',[UserController::class, 'index'])->name('user.index');
    Route::get('/user/add',[UserController::class, 'add'])->name('user.add');
    Route::post('/user/store',[UserController::class, 'store'])->name('user.store');
    Route::get('/user/{id}/edit',[UserController::class, 'edit'])->name('user.edit');
    Route::post('/user/{id}/update',[UserController::class, 'update'])->name('user.update');
    Route::delete('/user/{id}/destroy',[UserController::class, 'destroy'])->name('user.destroy');
    
    Route::get('/setting',[SettingController::class, 'index'])->name('setting.index');
    Route::post('/setting',[SettingController::class, 'store'])->name('setting.store');
    Route::post('/setting/{id}',[SettingController::class, 'update'])->name('setting.update');
    Route::post('/setting/{id}/clear',[SettingController::class, 'clear'])->name('setting.clear');
});