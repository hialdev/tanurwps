<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use App\Models\Task;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $role = auth()->user() !== null ? auth()->user()->getRoleNames()[0] : '';
        
        //$isAdmin = in_array($role, ['admin', 'developer']);
        $count = (object) [
            'stage' => Stage::count(),
            'task' => Task::count(),
        ];
        // if($isAdmin){
            return view('dashboard.admin', compact('count'));
        //}
        
    }

}
