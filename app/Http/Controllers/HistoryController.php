<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
   //index
   public function index(Request $request)
   {
      $histories = History::where('agent_id', session('agent_id'))->orderBy('created_at', 'desc')->get();

      if ($request->wantsJson() || $request->is('api/*')) {
         return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Histories retrieved successfully',
            'data' => $histories->values(),
         ]);
      }
      return view('mobile.history.index', compact('histories'));
   }
}
