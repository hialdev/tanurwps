<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\TanurController;
use App\Models\History;
use App\Models\WorkspaceStage;
use App\Models\WorkspaceStageApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StageApprovalController extends Controller
{
    protected $tanurapi = null;

    public function __construct()
    {
        $this->tanurapi = new TanurController();
    }

    //Send Approval
    public function send($workspace_id, $stage_id){
        $wstage = WorkspaceStage::where('workspace_id', $workspace_id)->where('stage_id', $stage_id)->first();
        try {
            $wstage->finished_at = now();
            $wstage->status = '0';

            $fetch = $this->tanurapi->getAgentDetail(session('agent_id'));
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
                    $history->type = 'stage';
                    $history->message = 'Pengajuan Stage '.$wstage->stage->name.' dari '.$fetch['data']['agent']['name'];
                    $history->color = 'warning';
                    $history->save();
                }
            }
            $wstage->save();

            $history = new History();
            $history->agent_id = $wstage->workspace->agent_id;
            $history->relation_id = $wstage->id;
            $history->type = 'stage';
            $history->message = 'Mengajukan Stage '.$wstage->stage->name;
            $history->color = 'warning';
            $history->save();

            return back()->with('success', 'Berhasil mengajukan Stage '.$wstage->stage->name.' ke Superior / Approver');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengajukan Stage: ' . $e->getMessage());
        }
    }

    //show
    public function show($approval_id)
    {
        $approval = WorkspaceStageApproval::findOrFail($approval_id);
        return view('mobile.approval.stage', compact('approval'));
    }

    //Decision
    public function decision(Request $request, $approval_id)
    {
        $approval = WorkspaceStageApproval::findOrFail($approval_id);
        if(session('agent_id') != $approval->approver_id){
            return back()->with('error', 'Anda tidak memiliki akses untuk memutuskan pengajuan ini');
        }
        if($approval->status != '0'){
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya');
        }
        if($approval->workspaceStage->status != '0'){
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya');
        }
        if($approval->workspaceStage->workspace->status == '4'){
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
            if($wstage->is_approved){
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
            $history->message = ($request->decision === 'approve' ? 'Menyetujui' : 'Menolak' ).' Stage '.$wstage->stage->name;
            $history->color = $request->decision === 'approve' ? 'success' : 'danger';
            $history->save();

            $history = new History();
            $history->agent_id = $wstage->workspace->agent_id;
            $history->relation_id = $wstage->id;
            $history->type = 'stage';
            $history->message = 'Salah Satu Approver '.($request->decision === 'approve' ? 'Menyetujui' : 'Menolak' ).' Stage '.$wstage->stage->name;
            $history->color = $request->decision === 'approve' ? 'success' : 'danger';
            $history->save();

            if ($wstage->workspace->isAllStageApproved()) {
                $wstage->workspace->status = '4';
                $wstage->workspace->save();

                $history = new History();
                $history->agent_id = $wstage->workspace->agent_id;
                $history->relation_id = $wstage->workspace->id;
                $history->type = 'workspace';
                $history->message = 'Workspace '.$wstage->workspace->name. ' Selesai';
                $history->color = 'success';
                $history->save();
            }

            return back()->with('success', $request->decision === 'approve' ? 'Pengajuan Stage berhasil disetujui' : 'Pengajuan Stage berhasil ditolak');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateDecision(Request $request, $approval_id)
    {
        $approval = WorkspaceStageApproval::findOrFail($approval_id);
        if(session('agent_id') != $approval->approver_id){
            return back()->with('error', 'Anda tidak memiliki akses untuk memutuskan pengajuan ini');
        }
        if($approval->workspaceStage->is_approved){
            return back()->with('error', 'Pengajuan ini sudah disetujui oleh semua Superior Level / Approver');
        }
        if($approval->workspaceStage->workspace->status == '4'){
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
            if($wstage->is_approved){
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
            $history->message = '[Diperbarui] '.($request->decision === 'approve' ? 'Menyetujui' : 'Menolak' ).' Stage '.$wstage->stage->name;
            $history->color = $request->decision === 'approve' ? 'success' : 'danger';
            $history->save();

            $history = new History();
            $history->agent_id = $wstage->workspace->agent_id;
            $history->relation_id = $wstage->id;
            $history->type = 'stage';
            $history->message = '[Diperbarui] Salah Satu Approver '.($request->decision === 'approve' ? 'Menyetujui' : 'Menolak' ).' Stage '.$wstage->stage->name;
            $history->color = $request->decision === 'approve' ? 'success' : 'danger';
            $history->save();

            if ($wstage->workspace->isAllStageApproved()) {
                $wstage->workspace->status = '4';
                $wstage->workspace->save();

                $history = new History();
                $history->agent_id = $wstage->workspace->agent_id;
                $history->relation_id = $wstage->workspace->id;
                $history->type = 'workspace';
                $history->message = 'Workspace '.$wstage->workspace->name. ' Selesai';
                $history->color = 'success';
                $history->save();
            }

            return back()->with('success', $request->decision === 'approve' ? 'Pengajuan Stage berhasil disetujui' : 'Pengajuan Stage berhasil ditolak');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
