<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\TanurController;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class AgentController extends Controller
{
    protected $tanurapi = null;
    public function __construct()
    {
        $this->tanurapi = new TanurController();
    }

    //index
    public function index(Request $request)
    {
        $filter = (object) [
            'q' => $request->get('search', ''),
            'field' => $request->get('field', 'name'),
            'order' => $request->get('order') === 'oldest' ? 'asc' : 'desc',
        ];

        // Jumlah data yang akan diambil per halaman (limit)
        $limit = 1000;
        $allAgents = []; // Menyimpan semua agen dari setiap halaman

        // Mengambil data dalam beberapa batch (pagination) hingga semua data terkumpul
        $offset = 0;
        do {
            $fetchAgents = Cache::remember("agents_cache_offset_{$offset}", now()->addMinutes(10), function () use ($limit, $offset) {
                return $this->tanurapi->getAgentList($limit, $offset); // Ambil data dengan limit dan offset
            });

            if ($fetchAgents['status'] && isset($fetchAgents['data']['agents'])) {
                $allAgents = array_merge($allAgents, $fetchAgents['data']['agents']);
            }

            $offset += $limit; // Menambah offset untuk permintaan berikutnya
        } while (count($fetchAgents['data']['agents']) === $limit); // Terus ambil data selama hasilnya sebanyak limit

        // Pastikan data agen tersedia
        if (empty($allAgents)) {
            return redirect()->back()->with('error', 'Data agen tidak ditemukan.');
        }

        // Filter dan sorting manual berdasarkan input
        $filtered = collect($allAgents)->filter(function ($agent) use ($filter) {
            return str_contains(strtolower($agent['name']), strtolower($filter->q)) || 
                   str_contains(strtolower($agent['agent_no']), strtolower($filter->q)) ||
                   str_contains(strtolower($agent['level']), strtolower($filter->q));
        })->sortBy($filter->field, SORT_NATURAL | SORT_FLAG_CASE, $filter->order === 'asc')->values();

        // Pagination secara manual
        $page = $request->get('page', 1);
        $perPage = 50; // Jumlah data yang ditampilkan per halaman
        $agents = new LengthAwarePaginator(
            $filtered->forPage($page, $perPage),
            $filtered->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $fields = ['name', 'agent_no', 'level'];
        // dd($agents);
        return view('agents.index', compact('agents', 'filter', 'fields'));
    }

    public function detail(){
        $fetch = $this->tanurapi->getAgentDetail(session('agent_id'));
        dd($fetch);
    }

}
