<?php

namespace App\Http\Controllers;

use App\Models\WorkspaceApproval;
use App\Models\WorkspaceStageApproval;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    //index
    public function index($id)
    {
        $workspace_approvals = WorkspaceApproval::where('approver_id', $id)->get();
        $stage_approvals = WorkspaceStageApproval::where('approver_id', $id)->get();
        return view('mobile.approval.index', compact('workspace_approvals', 'stage_approvals'));
    }
}
