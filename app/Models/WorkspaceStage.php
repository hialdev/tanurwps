<?php

namespace App\Models;

use App\Http\Controllers\Api\TanurController;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkspaceStage extends Model
{
    use HasFactory;
    protected $table = 'workspace_stages';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) \Illuminate\Support\Str::uuid();
        });
    }
    public function workspace()
    {
        return $this->belongsTo(Workspace::class, 'workspace_id', 'id');
    }
    public function stage()
    {
        return $this->belongsTo(Stage::class, 'stage_id', 'id');
    }
    public function workspaceTasks()
    {
        return $this->hasMany(WorkspaceTask::class, 'workspace_stage_id', 'id');
    }

    public function calculateScore()
    {
        $totalScore = 0;
        $reduceScore = 0;
        $finalScore = 0;

        // Calculate total score from finished tasks
        foreach ($this->workspaceTasks as $wtask) {
            if ($wtask->finished_at != null) {
                $totalScore += $wtask->score;
            }
        }

        // Check if deadline has passed
        $checkDeadline = now()->toDateString() > $this->deadline_at;

        if ($checkDeadline) {
            $daysPastDeadline = now()->diffInDays(\Carbon\Carbon::parse($this->deadline_at), false);
            $deadlineDays = $this->stage->deadline_days; // Get deadline_days from related Stage model

            $penaltyRate = $totalScore / $deadlineDays; // Penalty rate per day
            $reduceScore = $penaltyRate * $daysPastDeadline;

        }
        $finalScore = $totalScore - $reduceScore;
        $finalScore = max($totalScore, 0);

        return [
            'total' => $totalScore,
            'reduce' => $reduceScore,
            'final' => $finalScore,
        ];
    }
    
    public function deadlineCount()
    {
        $approved_at = $this->workspace->approved_at;
        $deadline_days = $this->stage->deadline_days;
        $workspace_approved_at = Carbon::parse($approved_at);

        if (!$workspace_approved_at) {
            return 'Deadline tidak tersedia';
        }

        $deadline_date = $workspace_approved_at->addDays($deadline_days);
        $remaining_days = now()->diffInDays($deadline_date, false);

        if ($remaining_days < 0) {
            return 'Deadline telah berlalu';
        }
        $messages = [
            'still' => ['text' => 'Sisa ' . $remaining_days . ' hari lagi', 'color' => 'secondary'],
            'short' => ['text' => 'Sisa ' . abs($remaining_days) . ' hari lagi', 'color' => 'warning'],
            'expired' => ['text' => 'Deadline telah berlalu', 'color' => 'muted'],
            'today' => ['text' => 'Hari ini adalah deadline', 'color' => 'danger'],
        ];

        $response = [
            'remaining_days' => $remaining_days,
            'deadline_date' => $deadline_date->format('Y-m-d'),
        ];
        if ($remaining_days > 0) {
            $response['message'] = $messages['still'];
        } elseif ($remaining_days > 0 && $remaining_days < 8) {
            $response['message'] = $messages['short'];
        } elseif ($remaining_days == 0) {
            $response['message'] = $messages['today'];
        } elseif ($remaining_days < 0) {
            $response['message'] = $messages['expired'];
        }
        return $response;
    }

    public function getIsApprovedAttribute(){
        return $this->approvals()->count() > 0 && $this->approvals()->where('status', '1')->count() === $this->approvals()->count();
    }

    public function getStatus()
    {
        $now = (string) $this->status;
        if(!$this->finished_at) return ['color' => null, 'name' => null];

        $statuses = [
            '0' => ['color' => 'warning', 'name' => 'Menunggu'],
            '1' => ['color' => 'success', 'name' => 'Disetujui'],
            '2' => ['color' => 'danger', 'name' => 'Ditolak'],
        ];
        return $statuses[$now] ?? ['color' => 'warning', 'name' => 'Menunggu'];
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

    public function approvals(){
        return $this->hasMany(WorkspaceStageApproval::class, 'workspace_stage_id', 'id');
    }
}
