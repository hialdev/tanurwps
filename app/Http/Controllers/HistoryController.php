<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    //index
    public function index()
    {
        $histories = History::where('agent_id', session('agent_id'))->get();
        return view('mobile.history.index', compact('histories'));
    }
}
