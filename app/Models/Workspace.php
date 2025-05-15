<?php

namespace App\Models;

use App\Http\Controllers\Api\TanurController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;
    protected $table = 'workspaces';

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) \Illuminate\Support\Str::uuid();
            $model->code = self::generateCode();
        });

        static::deleting(function ($model) {
            if($model->approvals->count() > 0){
                foreach ($model->approvals as $approval) {
                    $approval->delete();
                }
            }
            if($model->pilgrims->count() > 0){
                foreach ($model->pilgrims as $pilgrim) {
                    $pilgrim->delete();
                }
            }
        });
    }

    private static function generateCode()
    {
        $code = 'WS-' . strtoupper(\Illuminate\Support\Str::random(8));
        while (self::where('code', $code)->exists()) {
            $code = 'WS-' . strtoupper(\Illuminate\Support\Str::random(8));
        }
        return $code;
    }

    public function getStatus(){
        $now = (string) $this->status;
        $statuses = [
            '0' => ['color' => 'secondary', 'name' => 'Pending', 'message' => 'Menunggu persetujuan dari seluruh Superior Level'],
            '1' => ['color' => 'warning', 'name' => 'Berjalan', 'message' => null],
            '2' => ['color' => 'dark', 'name' => 'Pengajuan Stage', 'message' => 'Terdapat stage yang sedang dalam proses pengajuan'],
            '3' => ['color' => 'danger', 'name' => 'Stage Ditolak', 'message' => 'Pengajuan stage ditolak oleh salah satu atau lebih dari seluruh Superior Level'],
            '4' => ['color' => 'success', 'name' => 'Selesai', 'message' => null],
            '5' => ['color' => 'danger', 'name' => 'Ditolak', 'message' => 'Pengajuan Workspace ditolak oleh salah satu atau lebih dari seluruh Superior Level'],
        ];
        return $statuses[$now] ?? ['color' => 'secondary', 'name' => 'Unknown', 'message' => 'Status tidak diketahui'];
    }

    public function getAllStageApprovedAttribute(){
        $wstage = WorkspaceStage::where('workspace_id', $this->id)->count();
        $wstageApproved = WorkspaceStage::where('workspace_id', $this->id)->whereNotNull('approved_at')->count();
        if($wstage > 0 && $wstage == $wstageApproved) return true;
        return false;
    }

    public function pilgrims(){
        return $this->hasMany(WorkspacePilgrim::class, 'workspace_id', 'id');
    }

    public function approvals(){
        return $this->hasMany(WorkspaceApproval::class, 'workspace_id', 'id');
    }

    public function workspaceStages(){
        return $this->hasMany(WorkspaceStage::class, 'workspace_id', 'id');
    }

    public function getLiveScoreAttribute(){
        $stages = $this->workspaceStages()->whereNotNull('approved_at')->get();
        $totalScore = 0;
        if($stages->count() > 0){
            foreach ($stages as $stage) {
                $totalScore += $stage->calculateScore()['final'];
            }
        }
        return $totalScore;
    }

    public function stageAnalytic(){
        $total_stages = Stage::whereHas('tasks')->count();
        if($this->all_stage_approved) $total_stages = $this->workspaceStages->count();
        $total_stage_finished = $this->workspaceStages->count() > 0 ? $this->workspaceStages->whereNotNull('approved_at')->count() : 0;
        return (object) [
            'total' => $total_stages,
            'finished' => $total_stage_finished,
            'percentage' => $total_stages > 0 ? round(($total_stage_finished / $total_stages) * 100, 2) : 0,
        ];
    }

    public function allWorkspaceTasks()
    {
        return $this->workspaceStages->flatMap(function ($stage) {
            return $stage->workspaceTasks;
        });
    }

    public function taskAnalytic()
    {
        $total_tasks = Task::count();
        if($this->all_stage_approved) $total_tasks = $this->allWorkspaceTasks()->count();
        $total_task_finished = $this->allWorkspaceTasks()->whereNotNull('finished_at')->count();

        return (object) [
            'total' => $total_tasks,
            'finished' => $total_task_finished,
            'percentage' => $total_tasks > 0
                ? round(($total_task_finished / $total_tasks) * 100, 2)
                : 0,
        ];
    }

    public function getIsApprovedAttribute(){
        return $this->approvals()->count() > 0 && $this->approvals()->where('status', '1')->count() === $this->approvals()->count();
    }

    public function getHasApprovedAttribute(){
        return $this->approvals()->count() > 0 && $this->approvals()->where('status', '1')->count() > 0;
    }

    public function getApproversAttribute(){
        $tanurApi = new TanurController();
        $approvers = [];
        foreach ($this->approvals as $approval) {
            $data = $tanurApi->getAgentDetail($approval->approver_id);
            if (isset($data['data']['agent'])) {
                $approvers[] = (object) $data['data']['agent'];
            }
        }
        return $approvers;
    }

    public function getApproverStatusAttribute(){
        $statuses = [];
        foreach ($this->approvals as $approval) {
            $statuses[$approval->approver_id] = $approval->getStatus();
            $statuses[$approval->approver_id]['reason'] = $approval->reason ?? 'Belum ada Keputusan';
            $statuses[$approval->approver_id]['attachment'] = $approval->attachment;
        }
        return $statuses;
    }

    public function getRequesterAttribute(){
        $tanurApi = new TanurController();
        $data = $tanurApi->getAgentDetail($this->agent_id);
        return (object) $data['data']['agent'] ?? null;
    }

    public function isAllStageApproved(){
        $totalStages = $this->workspaceStages()->count();
        $approvedStages = $this->workspaceStages()->where('status', '1')->count();
        return $totalStages > 0 && $totalStages === $approvedStages;
    }
    
}
