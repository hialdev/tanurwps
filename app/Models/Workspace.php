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

    public function pilgrims(){
        return $this->hasMany(WorkspacePilgrim::class, 'workspace_id', 'id');
    }

    public function approvals(){
        return $this->hasMany(WorkspaceApproval::class, 'workspace_id', 'id');
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
    
}
