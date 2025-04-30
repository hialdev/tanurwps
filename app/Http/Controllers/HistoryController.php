<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HistoryController extends Controller
{
    //index
    public function index($id)
    {
        return view('mobile.history.index');
    }
}
