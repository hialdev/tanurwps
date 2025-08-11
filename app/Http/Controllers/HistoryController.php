<?php

namespace App\Http\Controllers;

use App\Helpers\ApiTransformer;
use App\Models\History;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
   //index
   public function index(Request $request)
   {
      $filter = (object) [
         'limit' => $request->get('limit', 20),
         'page' => $request->get('page', 1),
      ];

      $histories = History::where('agent_id', session('agent_id'))
         ->orderBy('created_at', 'desc')
         ->paginate($filter->limit, ['*'], 'page', $filter->page);

      if ($request->wantsJson() || $request->is('api/*')) {
         return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Histories retrieved successfully',
            'data' => [
               'pagination' => [
                  'current_page' => $histories->currentPage(),
                  'last_page' => $histories->lastPage(),
                  'per_page' => $histories->perPage(),
                  'total' => $histories->total(),
               ],
               'histories' => $histories->values()->map(fn($hst) => ApiTransformer::normalizeHistory($hst)),
               'filter' => $filter,
            ], 
         ]);
      }

      return view('mobile.history.index', compact('histories'));
   }

}
