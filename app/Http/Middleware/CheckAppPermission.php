<?php

namespace App\Http\Middleware;

use App\Models\Application;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class CheckAppPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil APP_CODE dari .env
        $appCode = env('APP_CODE', 'SSO');
        $name = Application::where('code', $appCode)->first();
        // Periksa apakah user sudah login
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        $user = User::find(Auth::user()->id);
        $permissionArray = $user->roles[0]->permissions->pluck('name')->toArray();
        // Periksa apakah user memiliki permission sesuai APP_CODE
        if (!in_array($name->name, $permissionArray) && !in_array(strtolower($name->name), $permissionArray)) {
            abort(403, 'You do not have permission to access this application.');
        }

        return $next($request);
    }
}
