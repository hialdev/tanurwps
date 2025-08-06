<?php

namespace App\Http\Controllers;

use App\Helpers\ApiTransformer;
use App\Http\Controllers\Api\TanurController;
use App\Models\Agent;
use App\Models\History;
use App\Models\Stage;
use App\Models\Workspace;
use App\Models\WorkspaceApproval;
use App\Models\WorkspaceStage;
use App\Models\WorkspaceStageApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WorkspaceController extends Controller
{
   protected $tanurApi = null;

   public function __construct()
   {
      $this->tanurApi = new TanurController();
   }

   //index
   public function index(Request $request)
   {
      $apitanur = new TanurController();
      $data = $apitanur->getAgentDetail(session('agent_id'));
      $agent = (object) $data['data']['agent'] ?? null;

      $workspaces = Workspace::where('agent_id', session('agent_id'))->limit(5)->get();
      $workspace_approvals = WorkspaceApproval::where('approver_id', session('agent_id'))->where('status', '0')->limit(6)->get();
      $stage_approvals = WorkspaceStageApproval::where('approver_id', session('agent_id'))->where('status', '0')->limit(6)->get();
      $approvals = $workspace_approvals->merge($stage_approvals)->sortByDesc('created_at');

      $count = (object) [
         'total_score' => Agent::totalScore(session('agent_id')),
         'total_workspace' => $workspaces->count(),
         'workspace' => (object) [
            'ongoing' => $workspaces->where('status', '0')->count(),
            'finish' => $workspaces->where('status', '4')->count(),
            'rejected' => $workspaces->where('status', '5')->count(),
         ],
      ];

      // ✅ Transform hanya saat JSON
      if ($request->wantsJson() || $request->is('api/*')) {
         return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Workspace retrieved successfully',
            'data' => [
               'agent' => $agent,
               'workspaces' => $workspaces->map(fn($w) => ApiTransformer::transformWorkspace($w)),
               'approvals' => $approvals->map(fn($a) => ApiTransformer::transformApproval($a))->values(),
               'count' => $count,
            ],
         ]);
      }

      // ✅ Untuk blade tetap raw Eloquent model
      return view('mobile.workspace.index', compact('workspaces', 'agent', 'approvals', 'count'));
   }

   // Show
   public function show(Request $request, $workspace_id)
   {
      $workspace = Workspace::findOrFail($workspace_id);
      $stages = Stage::whereHas('tasks')->orderBy('order')->with('tasks')->get();
      $workstages = null;
      if ($workspace->all_stage_approved) {
         $workstages = WorkspaceStage::where('workspace_id', $workspace_id)->get();
         $wstages = $workstages->pluck('stage_id');
         $stages = Stage::whereHas('tasks')->whereIn('id', $wstages)->get();
      }
      if ($request->wantsJson() || $request->is('api/*')) {
         return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Workspace detail retrievied successfully',
            'data' => [
               'workspace' => ApiTransformer::transformWorkspace($workspace, false),
            ],
         ]);
      }
      return view('mobile.workspace.show', compact('workspace', 'stages'));
   }

   //List
   public function list(Request $request)
   {
      $filter = (object) [
         'q' => $request->get('search', ''),
         'field' => $request->get('field', 'code'),
         'order' => $request->get('order') === 'oldest' ? 'asc' : 'desc',
         'status' => $request->get('status', ''), // tambahkan filter status
         'limit' => $request->get('limit', 100),
         'page' => $request->get('page', 1),
      ];

      $query = Workspace::where('agent_id', session('agent_id'));

      if ($filter->q !== '') {
         $query->where(function ($q) use ($filter) {
            $q->where('name', 'like', '%' . $filter->q . '%')
               ->orWhere('description', 'like', '%' . $filter->q . '%');
         });
      }

      if ($filter->status !== '' && $filter->status !== null) {
         $query->where('status', (string)$filter->status);
      }

      $workspaces = $query->orderBy($filter->field, $filter->order)
         ->paginate($filter->limit, ['*'], 'page', $filter->page);

      if ($request->wantsJson() || $request->is('api/*')) {
         return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Workspace list retrievied successfully',
            'data' => [
               'pagination' => [
                  'current_page' => $workspaces->currentPage(),
                  'last_page' => $workspaces->lastPage(),
                  'per_page' => $workspaces->perPage(),
                  'total' => $workspaces->total(),
               ],
               'workspaces' => $workspaces->map(function($workspace) {
                                 return ApiTransformer::transformWorkspace($workspace);
                              }),
               'filter' => $filter,
            ],
         ]);
      }
      return view('mobile.workspace.list', compact('workspaces', 'filter'));
   }

   //Add
   public function add(Request $request)
   {
      $cities = \App\Models\City::orderBy('nama')->get();
      if ($request->wantsJson() || $request->is('api/*')) {
         return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Workspace add retrievied successfully',
            'data' => $cities,
         ]);
      }
      return view('mobile.workspace.add', compact('cities'));
   }

   //Store
   public function store(Request $request)
   {
      $validator = Validator::make($request->all(), [
         'name' => 'required|string|max:255',
         'description' => 'nullable|string',
         'product_type' => 'required|in:umroh,haji',
         'address' => 'required|string',
         'city' => 'required|string|max:255',
         'pic_name' => 'required|string|max:255',
         'pic_phone' => 'required|string|min:4|max:15',
         'pic_email' => 'nullable|email|max:255',
         'total_pilgrim_male' => 'nullable|numeric|min:0|max:999',
         'total_pilgrim_female' => 'nullable|numeric|min:0|max:999',
      ], [
         'total_pilgrim_male.min' => 'Total Jamaah Laki Laki minimal angka 0',
         'total_pilgrim_male.numeric' => 'Total Jamaah Laki Laki harus berupa angka',
         'total_pilgrim_female.min' => 'Total Jamaah Perempuan minimal angka 0',
         'total_pilgrim_female.numeric' => 'Total Jamaah Perempuan harus berupa angka',
      ]);

      if ($validator->fails()) {

         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 400,
               'message' => 'Input tidak valid: ' . implode(', ', array_keys($validator->failed())),
               'data' => null,
            ]);
         }

         return redirect()->back()
            ->withInput()
            ->with('error', 'Input tidak valid: ' . implode(', ', array_keys($validator->failed())));
      }

      if (($request->total_pilgrim_male < 1 || !$request->total_pilgrim_male) && ($request->total_pilgrim_female < 1 || !$request->total_pilgrim_female)) {
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 400,
               'message' => 'Tidak ada total jamaah yang dimasukkan.',
               'data' => null,
            ]);
         }
         return redirect()->back()->withInput()->with('error', 'Tidak ada total jamaah yang dimasukkan.');
      }

      try {
         DB::transaction(function () use ($request) {
            $workspace = new Workspace();
            $workspace->agent_id = session('agent_id');
            $workspace->name = $request->name;
            $workspace->description = $request->description;
            $workspace->address = $request->address;
            $workspace->city = $request->city;
            $workspace->pic_name = $request->pic_name;
            $workspace->pic_phone = $request->pic_phone;
            $workspace->pic_email = $request->pic_email;
            $workspace->product_type = $request->product_type;
            $workspace->status = '0';
            $workspace->total_pilgrim_male = $request->total_pilgrim_male ?? 0;
            $workspace->total_pilgrim_female = $request->total_pilgrim_female ?? 0;
            $workspace->save();

            $history = new History();
            $history->agent_id = session('agent_id');
            $history->relation_id = $workspace->id;
            $history->type = 'workspace';
            $history->message = 'Workspace dibuat dan diajukan ' . $workspace->name;
            $history->color = 'dark';
            $history->save();

            $this->tanurApi->notify(session('agent_id'), 1, 1, 1, 'Workspace dibuat dan diajukan ' . $workspace->name, "Membuat dan mengajukan workspace baru, silahkan lihat di aplikasi pada menu WPS", 1);

            //Get Superior Tanur API Agent Detail
            $fetch = $this->tanurApi->getAgentDetail(session('agent_id'));
            $superiors = $fetch['data']['superiors'] ?? null;
            if ($superiors) {
               foreach ($superiors as $superior) {
                  $workspaceApproval = new WorkspaceApproval();
                  $workspaceApproval->workspace_id = $workspace->id;
                  $workspaceApproval->approver_id = $superior['id'];
                  $workspaceApproval->status = '0';
                  $workspaceApproval->save();

                  $history = new History();
                  $history->agent_id = $superior['id'];
                  $history->relation_id = $workspaceApproval->id;
                  $history->type = 'workspace_approval';
                  $history->message = 'Pengajuan Workspace ' . $workspace->name . ' dari ' . $fetch['data']['agent']['name'];
                  $history->color = 'dark';
                  $history->save();

                  $this->tanurApi->notify($superior['id'], 1, 1, 1, 'Pengajuan Workspace ' . $workspace->name . ' dari ' . $fetch['data']['agent']['name'], "Terdapat pengajuan workspace baru, silahkan lihat di aplikasi pada menu WPS", 1);
               }
            } else if ($fetch['data']['agent']['id_level'] == "MD") {
               $workspaceApproval = new WorkspaceApproval();
               $workspaceApproval->workspace_id = $workspace->id;
               $workspaceApproval->approver_id = session('agent_id');
               $workspaceApproval->status = '0';
               $workspaceApproval->save();

               $history = new History();
               $history->agent_id = session('agent_id');
               $history->relation_id = $workspaceApproval->id;
               $history->type = 'workspace_approval';
               $history->message = 'Pengajuan Workspace ' . $workspace->name . ' dari ' . $fetch['data']['agent']['name'];
               $history->color = 'dark';
               $history->save();

               $this->tanurApi->notify(session('agent_id'), 1, 1, 1, 'Pengajuan Workspace ' . $workspace->name . ' dari ' . $fetch['data']['agent']['name'], "Terdapat pengajuan workspace baru, silahkan lihat di aplikasi pada menu WPS", 1);
            }
         });

         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => true,
               'code' => 200,
               'message' => 'Workspace created successfully',
               'data' => null,
            ]);
         }

         return redirect()->route('agent.workspace.list')->with('success', 'Workspace created successfully.');
      } catch (\Exception $e) {

         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 500,
               'message' => 'Failed to create workspace: ' . $e->getMessage(),
               'data' => null,
            ]);
         }
         return redirect()->back()->withInput()->with('error', 'Failed to create workspace: ' . $e->getMessage());
      }
   }

   //Edit
   public function edit(Request $request, $workspace_id)
   {
      $workspace = Workspace::findOrFail($workspace_id);
      $cities = \App\Models\City::orderBy('nama')->get();
      if ($request->wantsJson() || $request->is('api/*')) {
         return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Workspace edit retrievied successfully.',
            'data' => compact('workspace', 'cities'),
         ]);
      }
      return view('mobile.workspace.edit', compact('workspace', 'cities'));
   }

   //Update
   public function update(Request $request, $workspace_id)
   {
      $workspace = Workspace::findOrFail($workspace_id);
      if ($workspace->agent_id != session('agent_id')) {
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 401,
               'message' => 'Aksi Ilegal, Anda tidak bisa mengubah Workspace orang lain.',
               'data' => null,
            ]);
         }
         return redirect()->route('agent.workspace.show', $workspace_id)->with('error', 'Aksi Ilegal, Anda tidak bisa mengubah Workspace orang lain.');
      }
      if ($workspace->status != '0') {
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 401,
               'message' => 'Workspace tidak dapat diubah, status sudah bukan pending.',
               'data' => null,
            ]);
         }
         return redirect()->route('agent.workspace.show', $workspace_id)->with('error', 'Workspace tidak dapat diubah, status sudah bukan pending.');
      }
      if ($workspace->is_approved || $workspace->has_approved) {
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 401,
               'message' => 'Workspace tidak dapat diubah, telah disetujui oleh salah satu atau lebih dari seluruh Superior Level.',
               'data' => null,
            ]);
         }
         return redirect()->route('agent.workspace.show', $workspace_id)->with('error', 'Workspace tidak dapat diubah, telah disetujui oleh salah satu atau lebih dari seluruh Superior Level.');
      }

      $validator = Validator::make($request->all(), [
         'name' => 'required|string|max:255',
         'description' => 'nullable|string',
         'product_type' => 'required|in:umroh,haji',
         'address' => 'required|string',
         'city' => 'required|string|max:255',
         'pic_name' => 'required|string|max:255',
         'pic_phone' => 'required|string|min:4|max:15',
         'pic_email' => 'nullable|email|max:255',
         'total_pilgrim_male' => 'nullable|numeric|min:0|max:999',
         'total_pilgrim_female' => 'nullable|numeric|min:0|max:999',
      ], [
         'total_pilgrim_male.min' => 'Total Jamaah Laki Laki minimal angka 0',
         'total_pilgrim_male.numeric' => 'Total Jamaah Laki Laki harus berupa angka',
         'total_pilgrim_female.min' => 'Total Jamaah Perempuan minimal angka 0',
         'total_pilgrim_female.numeric' => 'Total Jamaah Perempuan harus berupa angka',
      ]);

      if ($validator->fails()) {
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 400,
               'message' => 'Input tidak valid: ' . implode(', ', array_keys($validator->failed())),
               'data' => null,
            ]);
         }
         return redirect()->back()
            ->withInput()
            ->with('error', 'Input tidak valid: ' . implode(', ', array_keys($validator->failed())));
      }

      if (($request->total_pilgrim_male < 1 || !$request->total_pilgrim_male) && ($request->total_pilgrim_female < 1 || !$request->total_pilgrim_female)) {
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 400,
               'message' => 'Tidak ada total jamaah yang dimasukkan.',
               'data' => null,
            ]);
         }
         return redirect()->back()->withInput()->with('error', 'Tidak ada total jamaah yang dimasukkan.');
      }

      try {
         DB::transaction(function () use ($request, $workspace) {
            $workspace->name = $request->name;
            $workspace->description = $request->description;
            $workspace->address = $request->address;
            $workspace->city = $request->city;
            $workspace->pic_name = $request->pic_name;
            $workspace->pic_phone = $request->pic_phone;
            $workspace->pic_email = $request->pic_email;
            $workspace->product_type = $request->product_type;
            $workspace->total_pilgrim_male = $request->total_pilgrim_male ?? 0;
            $workspace->total_pilgrim_female = $request->total_pilgrim_female ?? 0;
            $workspace->save();

            $history = new History();
            $history->agent_id = session('agent_id');
            $history->relation_id = $workspace->id;
            $history->type = 'workspace';
            $history->message = 'Memperbarui Workspace ' . $workspace->name;
            $history->color = 'dark';
            $history->save();

            $this->tanurApi->notify(session('agent_id'), 0, 0, 1, 'Memperbarui Workspace ' . $workspace->name, "Terdapat pembaruan pada workspace, silahkan lihat di aplikasi pada menu WPS", 1);
         });

         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => true,
               'code' => 200,
               'message' => 'Workspace updated successfully.',
               'data' => null,
            ]);
         }

         return redirect()->route('agent.workspace.show', $workspace_id)->with('success', 'Workspace updated successfully.');
      } catch (\Exception $e) {
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 500,
               'message' => 'Failed to update workspace: ' . $e->getMessage(),
               'data' => null,
            ]);
         }
         return redirect()->back()->withInput()->with('error', 'Failed to update workspace: ' . $e->getMessage());
      }
   }


   //Ajukan
   public function sendApproval(Request $request)
   {
      try {
         $workspace = Workspace::findOrFail(session('agent_id'));

         // Create a new WorkspaceApproval
         $workspaceApproval = new \App\Models\WorkspaceApproval();
         $workspaceApproval->workspace_id = $workspace->id;
         $workspaceApproval->approver_id = auth()->id(); // Assuming the current user is the approver
         $workspaceApproval->status = 0; // Pending approval
         $workspaceApproval->save();

         // Update the workspace status to 1 (submitted for approval)
         $workspace->status = 1;
         $workspace->save();

         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => true,
               'code' => 200,
               'message' => 'Workspace submitted for approval successfully.',
               'data' => null,
            ]);
         }
         return redirect()->route('agent.workspace.list')->with('success', 'Workspace submitted for approval successfully.');
      } catch (\Exception $e) {
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 500,
               'message' => 'Failed to submit workspace for approval: ' . $e->getMessage(),
               'data' => null,
            ]);
         }
         return redirect()->back()->with('error', 'Failed to submit workspace for approval: ' . $e->getMessage());
      }
   }

   //Hapus
   public function destroy(Request $request, $workspace_id)
   {
      try {
         $workspace = Workspace::findOrFail($workspace_id);
         if ($workspace->agent_id != session('agent_id')) {
            if ($request->wantsJson() || $request->is('api/*')) {
               return response()->json([
                  'success' => false,
                  'code' => 401,
                  'message' => 'Aksi Ilegal, Anda tidak bisa mengubah Workspace orang lain.',
                  'data' => null,
               ]);
            }
            return redirect()->back()->with('error', 'Aksi Ilegal, Anda tidak bisa mengubah Workspace orang lain.');
         }
         if ($workspace->status != '0') {
            if ($request->wantsJson() || $request->is('api/*')) {
               return response()->json([
                  'success' => false,
                  'code' => 401,
                  'message' => 'Workspace tidak dapat dihapus, status sudah bukan pending.',
                  'data' => null,
               ]);
            }
            return redirect()->back()->with('error', 'Workspace tidak dapat dihapus, status sudah bukan pending.');
         }
         if ($workspace->is_approved || $workspace->has_approved) {
            if ($request->wantsJson() || $request->is('api/*')) {
               return response()->json([
                  'success' => false,
                  'code' => 401,
                  'message' => 'Workspace tidak dapat dihapus, telah disetujui oleh salah satu atau lebih dari seluruh Superior Level.',
                  'data' => null,
               ]);
            }
            return redirect()->back()->with('error', 'Workspace tidak dapat dihapus, telah disetujui oleh salah satu atau lebih dari seluruh Superior Level.');
         }
         $workspace->delete();

         $history = new History();
         $history->agent_id = session('agent_id');
         $history->relation_id = $workspace->id;
         $history->type = 'workspace';
         $history->message = 'Menghapus Workspace ' . $workspace->name;
         $history->color = 'danger';
         $history->save();

         $this->tanurApi->notify(session('agent_id'), 0, 0, 1, 'Menghapus Workspace ' . $workspace->name, "Terdapat penghapusan workspace, silahkan lihat di aplikasi pada menu WPS", 1);

         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => true,
               'code' => 200,
               'message' => 'Workspace deleted successfully',
               'data' => null,
            ]);
         }
         return redirect()->route('agent.workspace.list')->with('success', 'Workspace deleted successfully.');
      } catch (\Exception $e) {
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 500,
               'message' => 'Failed to delete workspace: ' . $e->getMessage(),
               'data' => null,
            ]);
         }
         return redirect()->back()->with('error', 'Failed to delete workspace: ' . $e->getMessage());
      }
   }
}
