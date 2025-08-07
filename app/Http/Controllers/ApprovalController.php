<?php

namespace App\Http\Controllers;

use App\Helpers\ApiTransformer;
use App\Models\History;
use App\Models\Stage;
use App\Models\WorkspaceApproval;
use App\Models\WorkspaceStage;
use App\Models\WorkspaceStageApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ApprovalController extends Controller
{
   protected $tanurApi;

   public function __construct()
   {
      $this->tanurApi = new \App\Http\Controllers\Api\TanurController();
   }
   //index
   // Index - supports both web and API response
   public function index(Request $request)
   {
      $search = $request->get('search', '');
      $status = $request->get('status', '');

      $filter = (object) [
         'q' => $request->get('search', ''),
         'status' => $request->get('status', ''),
         'limit' => (int) $request->get('limit', 15),
         'page' => (int) $request->get('page', 1),
      ];

      // Ambil semua approval, lalu gabungkan dan sort
      $workspace_approvals = WorkspaceApproval::with(['workspace'])
         ->where('approver_id', session('agent_id'))
         ->when($filter->status !== '', fn($q) => $q->where('status', 'LIKE', '%' . (string) $filter->status))
         ->when($filter->q, fn($q) => $q->where(function ($q) use ($filter) {
            $q->where('note', 'like', "%{$filter->q}%")
               ->orWhereHas('workspace', fn($q) => $q->where('name', 'like', "%{$filter->q}%"));
         }))
         ->get();

      $stage_approvals = WorkspaceStageApproval::where('approver_id', session('agent_id'))
         ->when($filter->status !== '', fn($q) => $q->where('status', 'LIKE', '%' . (string) $filter->status))
         ->when($filter->q, fn($q) => $q->where(function ($q) use ($filter) {
            $q->where('note', 'like', "%{$filter->q}%");
         }))
         ->get();

      $merged = $workspace_approvals->merge($stage_approvals)->sortByDesc('created_at')->values();

      // Manual pagination (karena merge di-collection)
      $total = $merged->count();
      $slice = $merged->slice(($filter->page - 1) * $filter->limit, $filter->limit)->values();

      if ($request->wantsJson() || $request->is('api/*')) {
         return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Approvals retrieved successfully',
            'data' => [
               'pagination' => [
                  'current_page' => $filter->page,
                  'last_page' => ceil($total / $filter->limit),
                  'per_page' => $filter->limit,
                  'total' => $total,
               ],
               'approvals' => $slice->map(fn($approval) => ApiTransformer::transformApproval($approval, true)),
               'filter' => $filter,
            ],
         ]);
      }

      // Untuk tampilan web
      $approvals = $merged;
      return view('mobile.approval.index', compact('approvals', 'filter', 'search', 'status'));
   }


   //show
   public function show(Request $request, $approval_id)
   {
      $approval = WorkspaceApproval::find($approval_id);

      if (!$approval) {
         return abort(404, 'Approval not found');
      }

      // If request expects JSON (API), return JSON response
      if ($request->wantsJson() || $request->is('api/*')) {
         return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Approval retrieved successfully',
            'data' => [
               'approval' => ApiTransformer::transformApproval($approval, false),
               'workspace' => ApiTransformer::transformWorkspace($approval->workspace, true, false)
            ],
         ]);
      }

      return view('mobile.approval.show', compact('approval'));
   }

   //Decision
   public function decision(Request $request, $approval_id)
   {
      $approval = WorkspaceApproval::findOrFail($approval_id);
      if (session('agent_id') != $approval->approver_id) {
         // If request expects JSON (API), return JSON response
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 401,
               'message' => 'Anda tidak memiliki akses untuk memutuskan pengajuan ini',
               'data' => null,
            ]);
         }
         return back()->with('error', 'Anda tidak memiliki akses untuk memutuskan pengajuan ini');
      }
      if ($approval->status != '0') {
         // If request expects JSON (API), return JSON response
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 401,
               'message' => 'Pengajuan ini sudah diproses sebelumnya',
               'data' => null,
            ]);
         }

         return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya');
         
      }
      if ($approval->workspace->status != '0') {
         // If request expects JSON (API), return JSON response
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 401,
               'message' => 'Pengajuan ini sudah diproses sebelumnya',
               'data' => null,
            ]);
         }
         return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya');
      }

      try {
         $request->validate([
            'decision' => 'required|in:approve,reject',
            'reason' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,webp|max:5120',
         ]);

         $approval->status = $request->decision === 'approve' ? '1' : '2';
         $approval->reason = $request->reason;
         $approval->attachment = $request->file('attachment') ? $request->file('attachment')->store('approval/attachments', 'public') : null;
         $approval->approved_at = $request->decision === 'approve' ? now() : null;
         $approval->rejected_at = $request->decision === 'reject' ? now() : null;
         $approval->save();

         $workspace = $approval->workspace;
         if ($workspace->is_approved) {
            $workspace->status = '1';
            $workspace->approved_at = now();
            foreach (Stage::whereHas('tasks')->get() as $stage) {
               $wstage = new WorkspaceStage();
               $wstage->workspace_id = $approval->workspace->id;
               $wstage->stage_id = $stage->id;
               $wstage->deadline_at = $stage->deadlineCount($workspace->approved_at)['deadline_date'];
               $wstage->status = '0';
               $wstage->save();
            }
         } elseif ($request->decision === 'reject') {
            $workspace->status = '5';
         } else {
            $workspace->status = '0';
         }
         $workspace->save();

         $history = new History();
         $history->agent_id = session('agent_id');
         $history->relation_id = $approval->id;
         $history->type = 'workspace_approval';
         $history->message = ($request->decision === 'approve' ? 'Menyetujui' : 'Menolak') . ' Workspace ' . $workspace->name;
         $history->color = $request->decision === 'approve' ? 'success' : 'danger';
         $history->save();

         $this->tanurApi->notify($workspace->agent_id, 1, 1, 1, ($request->decision === 'approve' ? 'Menyetujui' : 'Menolak') . ' Workspace ' . $workspace->name, "Berhasil memberikan aksi terhadap approval, silahkan lihat di aplikasi pada menu WPS", 1);

         $history = new History();
         $history->agent_id = $workspace->agent_id;
         $history->relation_id = $workspace->id;
         $history->type = 'workspace';
         $history->message = 'Salah Satu Approver ' . ($request->decision === 'approve' ? 'Menyetujui' : 'Menolak') . ' Workspace ' . $workspace->name;
         $history->color = $request->decision === 'approve' ? 'success' : 'danger';
         $history->save();

         $this->tanurApi->notify($workspace->agent_id, 1, 1, 1, 'Salah Satu Approver ' . ($request->decision === 'approve' ? 'Menyetujui' : 'Menolak') . ' Workspace ' . $workspace->name, "Terdapat pembaruan status pada approval, silahkan lihat di aplikasi pada menu WPS", 1);
         // If request expects JSON (API), return JSON response
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => true,
               'code' => 200,
               'message' => $request->decision === 'approve' ? 'Pengajuan berhasil disetujui' : 'Pengajuan berhasil ditolak',
               'data' => null,
            ]);
         }

         return back()->with('success', $request->decision === 'approve' ? 'Pengajuan berhasil disetujui' : 'Pengajuan berhasil ditolak');
      } catch (\Exception $e) {
         // If request expects JSON (API), return JSON response
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 500,
               'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
               'data' => null,
            ]);
         }
         return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
      }
   }

   public function updateDecision(Request $request, $approval_id)
   {
      $approval = WorkspaceApproval::findOrFail($approval_id);
      if (session('agent_id') != $approval->approver_id) {
         // If request expects JSON (API), return JSON response
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 401,
               'message' => 'Anda tidak memiliki akses untuk memutuskan pengajuan ini',
               'data' => null,
            ]);
         }

         return back()->with('error', 'Anda tidak memiliki akses untuk memutuskan pengajuan ini');
      }
      if ($approval->workspace->is_approved && ($approval->workspace->status != '0' && $approval->workspace->status != '5')) {
         // If request expects JSON (API), return JSON response
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 401,
               'message' => 'Pengajuan ini sudah disetujui oleh semua Superior Level / Approver',
               'data' => null,
            ]);
         }

         return back()->with('error', 'Pengajuan ini sudah disetujui oleh semua Superior Level / Approver');
      }

      try {
         $request->validate([
            'decision' => 'required|in:approve,reject',
            'reason' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,webp|max:5120',
         ]);

         $approval->status = $request->decision === 'approve' ? '1' : '2';
         $approval->reason = $request->reason;
         if ($approval->attachment) {
            Storage::disk('public')->delete($approval->attachment);
         }
         $approval->attachment = $request->file('attachment') ? $request->file('attachment')->store('approval/attachments', 'public') : $approval->attachment;
         $approval->approved_at = $request->decision === 'approve' ? now() : null;
         $approval->rejected_at = $request->decision === 'reject' ? now() : null;
         $approval->save();

         $workspace = $approval->workspace;
         if ($workspace->is_approved) {
            $workspace->status = '1';
            $workspace->approved_at = now();
            foreach (Stage::whereHas('tasks')->get() as $stage) {
               $wstage = new WorkspaceStage();
               $wstage->workspace_id = $approval->workspace->id;
               $wstage->stage_id = $stage->id;
               $wstage->deadline_at = $stage->deadlineCount($workspace->approved_at)['deadline_date'];
               $wstage->status = '0';
               $wstage->save();
            }
         } elseif ($request->decision === 'reject') {
            $workspace->status = '5';
         } else {
            $workspace->status = '0';
         }
         $workspace->save();

         $history = new History();
         $history->agent_id = session('agent_id');
         $history->relation_id = $approval->id;
         $history->type = 'workspace_approval';
         $history->message = '[Diperbarui] ' . ($request->decision === 'approve' ? 'Menyetujui' : 'Menolak') . ' Workspace ' . $workspace->name;
         $history->color = $request->decision === 'approve' ? 'success' : 'danger';
         $history->save();

         $this->tanurApi->notify(session('agent_id'), 1, 1, 1, '[Diperbarui] ' . ($request->decision === 'approve' ? 'Menyetujui' : 'Menolak') . ' Workspace ' . $workspace->name, "Anda berhasil melakukan aksi terhadap approval, silahkan lihat di aplikasi pada menu WPS", 1);

         $history = new History();
         $history->agent_id = $workspace->agent_id;
         $history->relation_id = $workspace->id;
         $history->type = 'workspace';
         $history->message = '[Diperbarui] Salah Satu Approver ' . ($request->decision === 'approve' ? 'Menyetujui' : 'Menolak') . ' Workspace ' . $workspace->name;
         $history->color = $request->decision === 'approve' ? 'success' : 'danger';
         $history->save();

         $this->tanurApi->notify($workspace->agent_id, 1, 1, 1, '[Diperbarui] Salah Satu Approver ' . ($request->decision === 'approve' ? 'Menyetujui' : 'Menolak') . ' Workspace ' . $workspace->name, "Terdapat pembaruan status approval, silahkan lihat di aplikasi pada menu WPS", 1);
         // If request expects JSON (API), return JSON response
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => true,
               'code' => 200,
               'message' => $request->decision === 'approve' ? 'Pengajuan berhasil disetujui' : 'Pengajuan berhasil ditolak',
               'data' => null,
            ]);
         }

         return back()->with('success', $request->decision === 'approve' ? 'Pengajuan berhasil disetujui' : 'Pengajuan berhasil ditolak');
      } catch (\Exception $e) {
         // If request expects JSON (API), return JSON response
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 500,
               'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
               'data' => null,
            ]);
         }
         return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
      }
   }

   // -----------------------
   // Approval Controler API
   // -----------------------

   public function getApprovals(Request $request)
   {
      $apiKey = $request->input('apiKey');
      $agentId = $request->input('agent_id');
      $status = $request->input('status', 1);

      if ($apiKey !== env('API_KEY_ACCESS')) {
         return response()->json([
            'success' => false,
            'code' => 401,
            'message' => 'Unauthorized: Invalid API key',
            'data' => null,
         ], 401);
      }

      if (!$agentId) {
         return response()->json([
            'success' => false,
            'code' => 400,
            'message' => 'agent_id is required',
            'data' => null,
         ], 400);
      }

      try {
         // Query workspace_approvals
         $workspaceApprovals = WorkspaceApproval::select(
            'id',
            'workspace_id as reference_id',
            'approver_id',
            'status',
            'approved_at',
            'rejected_at',
            'reason',
            'attachment',
            'created_at',
            DB::raw("'workspace_approval' as type")
         )
            ->where('approver_id', $agentId)
            ->where('status', $status);

         // Query workspace_stage_approvals
         $stageApprovals = WorkspaceStageApproval::select(
            'id',
            'workspace_stage_id as reference_id',
            'approver_id',
            'status',
            'approved_at',
            'rejected_at',
            'reason',
            'attachment',
            'created_at',
            DB::raw("'workspace_stage_approval' as type")
         )
            ->where('approver_id', $agentId)
            ->where('status', $status);

         // Gabungkan dan urutkan berdasarkan created_at ascending
         $approvals = $workspaceApprovals->unionAll($stageApprovals)
            ->orderBy('created_at', 'asc')
            ->get();

         return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Approvals retrieved successfully',
            'data' => $approvals,
         ]);
      } catch (\Exception $e) {
         return response()->json([
            'success' => false,
            'code' => 500,
            'message' => 'Server error: ' . $e->getMessage(),
            'data' => null,
         ], 500);
      }
   }

   public function getApprovalBoolean(Request $request)
   {
      $apiKey = $request->input('apiKey');
      $agentId = $request->input('agent_id');
      $status = $request->input('status', 1);

      if ($apiKey !== env('API_KEY_ACCESS')) {
         return response()->json([
            'success' => false,
            'code' => 401,
            'message' => 'Unauthorized: Invalid API key',
            'data' => null,
         ], 401);
      }

      if (!$agentId) {
         return response()->json([
            'success' => false,
            'code' => 400,
            'message' => 'agent_id is required',
            'data' => null,
         ], 400);
      }

      try {
         $exists = WorkspaceApproval::where('approver_id', $agentId)
            ->where('status', $status)
            ->exists()
            || WorkspaceStageApproval::where('approver_id', $agentId)
            ->where('status', $status)
            ->exists();

         return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Approval existence checked successfully',
            'data' => $exists,
         ]);
      } catch (\Exception $e) {
         return response()->json([
            'success' => false,
            'code' => 500,
            'message' => 'Server error: ' . $e->getMessage(),
            'data' => null,
         ], 500);
      }
   }
}
