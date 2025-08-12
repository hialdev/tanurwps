<?php

namespace App\Http\Controllers;

use App\Helpers\ApiTransformer;
use App\Http\Controllers\Api\TanurController;
use App\Models\History;
use App\Models\WorkspaceStage;
use App\Models\WorkspaceStageApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StageApprovalController extends Controller
{
   protected $tanurApi;

   public function __construct()
   {
      $this->tanurApi = new \App\Http\Controllers\Api\TanurController();
   }

   //Send Approval
   public function send(Request $request, $workspace_id, $stage_id)
   {
      $wstage = WorkspaceStage::where('workspace_id', $workspace_id)->where('stage_id', $stage_id)->first();
      try {
         $wstage->finished_at = now();
         $wstage->status = '0';

         $fetch = $this->tanurApi->getAgentDetail(session('agent_id'));
         $superiors = $fetch['data']['superiors'] ?? null;
         if ($superiors) {
            foreach ($superiors as $superior) {
               $stageApproval = new WorkspaceStageApproval();
               $stageApproval->workspace_stage_id = $wstage->id;
               $stageApproval->approver_id = $superior['id'];
               $stageApproval->status = '0'; // Pending approval
               $stageApproval->save();

               $history = new History();
               $history->agent_id = $superior['id'];
               $history->relation_id = $stageApproval->id;
               $history->type = 'stage_approval';
               $history->message = 'Pengajuan Stage ' . $wstage->stage->name . ' dari ' . $fetch['data']['agent']['name'];
               $history->color = 'warning';
               $history->save();

               $this->tanurApi->notify($superior['id'], 1, 1, 1, 'Pengajuan Stage ' . $wstage->stage->name . ' dari ' . $fetch['data']['agent']['name'], "Terdapat pengajuan stage baru, silahkan lihat di aplikasi pada menu WPS", 1);
            }
         }else if ($fetch['data']['agent']['id_level'] == "MD") {
            $stageApproval = new WorkspaceStageApproval();
            $stageApproval->workspace_stage_id = $wstage->id;
            $stageApproval->approver_id = session('agent_id');
            $stageApproval->status = '0';
            $stageApproval->save();

            $history = new History();
            $history->agent_id = session('agent_id');
            $history->relation_id = $stageApproval->id;
            $history->type = 'stage_approval';
            $history->message = 'Pengajuan Stage ' . $wstage->stage->name . ' dari ' . $fetch['data']['agent']['name'];
            $history->color = 'warning';
            $history->save();

            $this->tanurApi->notify(session('agent_id'), 1, 1, 1, 'Pengajuan Stage ' . $wstage->stage->name . ' dari ' . $fetch['data']['agent']['name'], "Terdapat pengajuan stage baru, silahkan lihat di aplikasi pada menu WPS", 1);
         }
         $wstage->save();

         $history = new History();
         $history->agent_id = $wstage->workspace->agent_id;
         $history->relation_id = $wstage->id;
         $history->type = 'stage';
         $history->message = 'Mengajukan Stage ' . $wstage->stage->name;
         $history->color = 'warning';
         $history->save();

         $this->tanurApi->notify($wstage->workspace->agent_id, 1, 1, 1, 'Mengajukan Stage ' . $wstage->stage->name, "Berhasil mengajukan stage, silahkan lihat di aplikasi pada menu WPS", 1);

         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => true,
               'code' => 200,
               'message' => 'Berhasil mengajukan Stage ' . $wstage->stage->name . ' ke Superior / Approver',
               'data' => null,
            ]);
         }

         return back()->with('success', 'Berhasil mengajukan Stage ' . $wstage->stage->name . ' ke Superior / Approver');
      } catch (\Exception $e) {
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 500,
               'message' => 'Terjadi kesalahan saat mengajukan Stage: ' . $e->getMessage(),
               'data' => null,
            ]);
         }

         return back()->with('error', 'Terjadi kesalahan saat mengajukan Stage: ' . $e->getMessage());
      }
   }

   //show
   public function show(Request $request, $approval_id)
   {
      $approval = WorkspaceStageApproval::findOrFail($approval_id);
      if ($request->wantsJson() || $request->is('api/*')) {
         return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Stage approval retrevied successfully.',
            'data' => [
               'approval' => ApiTransformer::normalizeSapproval($approval, false),
               'workspace_stage' => ApiTransformer::normalizeWstage($approval->workspaceStage, true)
            ],
         ]);
      }
      return view('mobile.approval.stage', compact('approval'));
   }

   //Decision
   public function decision(Request $request, $approval_id)
   {
      $approval = WorkspaceStageApproval::findOrFail($approval_id);
      if (session('agent_id') != $approval->approver_id) {
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 401,
               'message' => 'Anda tidak memiliki akses untuk memutuskan pengajuan ini.',
               'data' => null,
            ]);
         }
         return back()->with('error', 'Anda tidak memiliki akses untuk memutuskan pengajuan ini');
      }
      if ($approval->status != '0') {
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 401,
               'message' => 'Pengajuan ini sudah diproses sebelumnya.',
               'data' => null,
            ]);
         }
         return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya');
      }
      if ($approval->workspaceStage->status != '0') {
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 401,
               'message' => 'Pengajuan ini sudah diproses sebelumnya.',
               'data' => null,
            ]);
         }
         return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya');
      }
      if ($approval->workspaceStage->workspace->status == '4') {
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 401,
               'message' => 'Workspace Telah Diselesaikan!',
               'data' => null,
            ]);
         }
         return back()->with('error', 'Workspace Telah Diselesaikan!');
      }

      try {
         $request->validate([
            'decision' => 'required|in:approve,reject',
            'reason' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,webp|max:5120',
         ]);

         $approval->status = $request->decision === 'approve' ? '1' : '2';
         $approval->reason = $request->reason;
         $approval->attachment = $request->file('attachment') ? $request->file('attachment')->store('approval/stage/attachments', 'public') : null;
         $approval->approved_at = $request->decision === 'approve' ? now() : null;
         $approval->rejected_at = $request->decision === 'reject' ? now() : null;
         $approval->save();

         $wstage = $approval->workspaceStage;
         if ($wstage->is_approved) {
            $wstage->status = '1';
            $wstage->total_score = $wstage->calculateScore()['total'];
            $wstage->reduce_score = $wstage->calculateScore()['reduce'];
            $wstage->final_score = $wstage->calculateScore()['final'];
            $wstage->approved_at = now();
         } elseif ($request->decision === 'reject') {
            $wstage->status = '2';
            $wstage->approved_at = null;
         } else {
            $wstage->status = '0';
         }
         $wstage->save();

         $history = new History();
         $history->agent_id = session('agent_id');
         $history->relation_id = $approval->id;
         $history->type = 'stage_approval';
         $history->message = ($request->decision === 'approve' ? 'Menyetujui' : 'Menolak') . ' Stage ' . $wstage->stage->name;
         $history->color = $request->decision === 'approve' ? 'success' : 'danger';
         $history->save();

         $this->tanurApi->notify($wstage->workspace->agent_id, 1, 1, 1, ($request->decision === 'approve' ? 'Menyetujui' : 'Menolak') . ' Stage ' . $wstage->stage->name, "Berhasil memberikan aksi terhadap approval, silahkan lihat di aplikasi pada menu WPS", 1, "wps_approval", ApiTransformer::normalizeApproval($approval, true, true));

         $history = new History();
         $history->agent_id = session('agent_id');
         $history->relation_id = $wstage->id;
         $history->type = 'stage';
         $history->message = 'Salah Satu Approver ' . ($request->decision === 'approve' ? 'Menyetujui' : 'Menolak') . ' Stage ' . $wstage->stage->name;
         $history->color = $request->decision === 'approve' ? 'success' : 'danger';
         $history->save();

         $this->tanurApi->notify(session('agent_id'), 1, 1, 1, 'Salah Satu Approver ' . ($request->decision === 'approve' ? 'Menyetujui' : 'Menolak') . ' Stage ' . $wstage->stage->name, "Terdapat status approval terbaru, silahkan lihat di aplikasi pada menu WPS", 1, "wps_workspace", ApiTransformer::normalizeMinimalWorkspace($wstage->workspace));

         if ($wstage->workspace->isAllStageApproved()) {
            $wstage->workspace->status = '4';
            $wstage->workspace->save();

            $history = new History();
            $history->agent_id = $wstage->workspace->agent_id;
            $history->relation_id = $wstage->workspace->id;
            $history->type = 'workspace';
            $history->message = 'Workspace ' . $wstage->workspace->name . ' Selesai';
            $history->color = 'success';
            $history->save();

            $this->tanurApi->notify($wstage->workspace->agent_id, 1, 1, 1, 'Workspace ' . $wstage->workspace->name . ' Selesai', "Workspace selesai!, silahkan lihat di aplikasi pada menu WPS", 1, "wps_workspace", ApiTransformer::normalizeMinimalWorkspace($wstage->workspace));
         }

         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => true,
               'code' => 200,
               'message' => $request->decision === 'approve' ? 'Pengajuan Stage berhasil disetujui' : 'Pengajuan Stage berhasil ditolak',
               'data' => null,
            ]);
         }
         return back()->with('success', $request->decision === 'approve' ? 'Pengajuan Stage berhasil disetujui' : 'Pengajuan Stage berhasil ditolak');
      } catch (\Exception $e) {
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
      $approval = WorkspaceStageApproval::findOrFail($approval_id);
      if (session('agent_id') != $approval->approver_id) {
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 401,
               'message' => 'Anda tidak memiliki akses untuk memutuskan pengajuan ini!',
               'data' => null,
            ]);
         }
         return back()->with('error', 'Anda tidak memiliki akses untuk memutuskan pengajuan ini');
      }
      if ($approval->workspaceStage->is_approved) {
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 401,
               'message' => 'Pengajuan ini sudah disetujui oleh semua Superior Level / Approver!',
               'data' => null,
            ]);
         }
         return back()->with('error', 'Pengajuan ini sudah disetujui oleh semua Superior Level / Approver');
      }
      if ($approval->workspaceStage->workspace->status == '4') {
         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => false,
               'code' => 401,
               'message' => 'Workspace Telah Diselesaikan!',
               'data' => null,
            ]);
         }
         return back()->with('error', 'Workspace Telah Diselesaikan!');
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
         $approval->attachment = $request->file('attachment') ? $request->file('attachment')->store('approval/stage/attachments', 'public') : $approval->attachment;
         $approval->approved_at = $request->decision === 'approve' ? now() : null;
         $approval->rejected_at = $request->decision === 'reject' ? now() : null;
         $approval->save();

         $wstage = $approval->workspaceStage;
         if ($wstage->is_approved) {
            $wstage->status = '1';
            $wstage->total_score = $wstage->calculateScore()['total'];
            $wstage->reduce_score = $wstage->calculateScore()['reduce'];
            $wstage->final_score = $wstage->calculateScore()['final'];
            $wstage->approved_at = now();
         } elseif ($request->decision === 'reject') {
            $wstage->status = '2';
            $wstage->approved_at = null;
         } else {
            $wstage->status = '0';
         }
         $wstage->save();

         $history = new History();
         $history->agent_id = session('agent_id');
         $history->relation_id = $approval->id;
         $history->type = 'stage_approval';
         $history->message = '[Diperbarui] ' . ($request->decision === 'approve' ? 'Menyetujui' : 'Menolak') . ' Stage ' . $wstage->stage->name;
         $history->color = $request->decision === 'approve' ? 'success' : 'danger';
         $history->save();

         $this->tanurApi->notify(session('agent_id'), 1, 1, 1, '[Diperbarui] ' . ($request->decision === 'approve' ? 'Menyetujui' : 'Menolak') . ' Stage ' . $wstage->stage->name, "Memperbarui aksi terhadap approval, silahkan lihat di aplikasi pada menu WPS", 1, "wps_approval", ApiTransformer::normalizeApproval($approval, true, true));


         $history = new History();
         $history->agent_id = $wstage->workspace->agent_id;
         $history->relation_id = $wstage->id;
         $history->type = 'stage';
         $history->message = '[Diperbarui] Salah Satu Approver ' . ($request->decision === 'approve' ? 'Menyetujui' : 'Menolak') . ' Stage ' . $wstage->stage->name;
         $history->color = $request->decision === 'approve' ? 'success' : 'danger';
         $history->save();

         $this->tanurApi->notify($wstage->workspace->agent_id, 1, 1, 1, '[Diperbarui] Salah Satu Approver ' . ($request->decision === 'approve' ? 'Menyetujui' : 'Menolak') . ' Stage ' . $wstage->stage->name, "Terdapat pembaruan status approval, silahkan lihat di aplikasi pada menu WPS", 1, "wps_workspace", ApiTransformer::normalizeMinimalWorkspace($wstage->workspace));

         if ($wstage->workspace->isAllStageApproved()) {
            $wstage->workspace->status = '4';
            $wstage->workspace->save();

            $history = new History();
            $history->agent_id = $wstage->workspace->agent_id;
            $history->relation_id = $wstage->workspace->id;
            $history->type = 'workspace';
            $history->message = 'Workspace ' . $wstage->workspace->name . ' Selesai';
            $history->color = 'success';
            $history->save();

            $this->tanurApi->notify($wstage->workspace->agent_id, 1, 1, 1, 'Workspace ' . $wstage->workspace->name . ' Selesai', "Workspace selesai!, silahkan lihat di aplikasi pada menu WPS", 1, "wps_workspace", ApiTransformer::normalizeMinimalWorkspace($wstage->workspace));
         }

         if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
               'success' => true,
               'code' => 200,
               'message' => $request->decision === 'approve' ? 'Pengajuan Stage berhasil disetujui' : 'Pengajuan Stage berhasil ditolak',
               'data' => null,
            ]);
         }

         return back()->with('success', $request->decision === 'approve' ? 'Pengajuan Stage berhasil disetujui' : 'Pengajuan Stage berhasil ditolak');
      } catch (\Exception $e) {
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
}
