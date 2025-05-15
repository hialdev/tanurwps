<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\Stage;
use App\Models\WorkspaceApproval;
use App\Models\WorkspaceStage;
use App\Models\WorkspaceStageApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApprovalController extends Controller
{
    //index
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $status = $request->get('status', '');

        $workspace_approvals = WorkspaceApproval::where('approver_id', session('agent_id'))
            ->when($status !== '', fn($q) => $q->where('status', 'LIKE', '%'.(string) $status))
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('note', 'like', "%{$search}%")
                ->orWhereHas('workspace', fn($q) => $q->where('name', 'like', "%{$search}%"));
            }))
            ->get();

        $stage_approvals = WorkspaceStageApproval::where('approver_id', session('agent_id'))
            ->when($status !== '', fn($q) => $q->where('status', 'LIKE', '%'.(string) $status))
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('note', 'like', "%{$search}%")
                ->orWhereHas('workspaceStage.workspace', fn($q) => $q->where('name', 'like', "%{$search}%"));
            }))
            ->get();

        $approvals = $workspace_approvals->merge($stage_approvals)->sortByDesc('created_at');

        return view('mobile.approval.index', compact('approvals', 'search', 'status'));
    }


    //show
    public function show($approval_id)
    {
        $approval = WorkspaceApproval::findOrFail($approval_id);
        return view('mobile.approval.show', compact('approval'));
    }

    //Decision
    public function decision(Request $request, $approval_id)
    {
        $approval = WorkspaceApproval::findOrFail($approval_id);
        if(session('agent_id') != $approval->approver_id){
            return back()->with('error', 'Anda tidak memiliki akses untuk memutuskan pengajuan ini');
        }
        if($approval->status != '0'){
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya');
        }
        if($approval->workspace->status != '0'){
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
            if($workspace->is_approved){
                $workspace->status = '1';
                $workspace->approved_at = now();
                foreach (Stage::whereHas('tasks')->get() as $stage){
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
            $history->message = ($request->decision === 'approve' ? 'Menyetujui' : 'Menolak' ).' Workspace '.$workspace->name;
            $history->color = $request->decision === 'approve' ? 'success' : 'danger';
            $history->save();

            $history = new History();
            $history->agent_id = $workspace->agent_id;
            $history->relation_id = $workspace->id;
            $history->type = 'workspace';
            $history->message = 'Salah Satu Approver '.($request->decision === 'approve' ? 'Menyetujui' : 'Menolak' ).' Workspace '.$workspace->name;
            $history->color = $request->decision === 'approve' ? 'success' : 'danger';
            $history->save();

            return back()->with('success', $request->decision === 'approve' ? 'Pengajuan berhasil disetujui' : 'Pengajuan berhasil ditolak');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateDecision(Request $request, $approval_id)
    {
        $approval = WorkspaceApproval::findOrFail($approval_id);
        if(session('agent_id') != $approval->approver_id){
            return back()->with('error', 'Anda tidak memiliki akses untuk memutuskan pengajuan ini');
        }
        if($approval->workspace->is_approved && ($approval->workspace->status != '0' && $approval->workspace->status != '5')){
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
            if($workspace->is_approved){
                $workspace->status = '1';
                $workspace->approved_at = now();
                foreach (Stage::whereHas('tasks')->get() as $stage){
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
            $history->message = '[Diperbarui] '.($request->decision === 'approve' ? 'Menyetujui' : 'Menolak' ).' Workspace '.$workspace->name;
            $history->color = $request->decision === 'approve' ? 'success' : 'danger';
            $history->save();

            $history = new History();
            $history->agent_id = $workspace->agent_id;
            $history->relation_id = $workspace->id;
            $history->type = 'workspace';
            $history->message = '[Diperbarui] Salah Satu Approver '.($request->decision === 'approve' ? 'Menyetujui' : 'Menolak' ).' Workspace '.$workspace->name;
            $history->color = $request->decision === 'approve' ? 'success' : 'danger';
            $history->save();

            return back()->with('success', $request->decision === 'approve' ? 'Pengajuan berhasil disetujui' : 'Pengajuan berhasil ditolak');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

}
