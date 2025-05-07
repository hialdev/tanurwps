<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\TanurController;
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
                }
            }
            $wstage->save();

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
                $wstage->approved_at = now();
            } elseif ($request->decision === 'reject') {
                $wstage->status = '2';
                $wstage->approved_at = null;
            } else {
                $wstage->status = '0';
            }
            $wstage->save();

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
                $wstage->approved_at = now();
            } elseif ($request->decision === 'reject') {
                $wstage->status = '2';
                $wstage->approved_at = null;
            } else {
                $wstage->status = '0';
            }
            $wstage->save();

            return back()->with('success', $request->decision === 'approve' ? 'Pengajuan Stage berhasil disetujui' : 'Pengajuan Stage berhasil ditolak');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
