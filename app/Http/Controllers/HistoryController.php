<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HistoryController extends Controller
{
    //index
    public function index()
    {
        return view('mobile.history.index');
    }
}
