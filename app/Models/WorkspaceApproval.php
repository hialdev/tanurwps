<?php

namespace App\Models;

use App\Http\Controllers\Api\TanurController;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkspaceApproval extends Model
{
    use HasFactory;
    protected $table = 'workspace_approvals';
    protected $keyType = 'string';
    public $incrementing = false;

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

    public function getRequesterAttribute(){
        $tanurApi = new TanurController();
        $data = $tanurApi->getAgentDetail($this->workspace->agent_id);
        return (object) $data['data']['agent'] ?? null;
    }

    public function getStatus()
    {
        $now = (string) $this->status;
        $statuses = [
            '0' => ['color' => 'warning', 'name' => 'Menunggu'],
            '1' => ['color' => 'success', 'name' => 'Disetujui'],
            '2' => ['color' => 'danger', 'name' => 'Ditolak'],
        ];
        return $statuses[$now] ?? ['color' => 'warning', 'name' => 'Menunggu'];
    }

    public function getTimeAgoAttribute()
    {
        $now = Carbon::now();
        $created = $this->created_at;

        $diffInSeconds = $created->diffInSeconds($now);
        if ($diffInSeconds < 60) {
            return $diffInSeconds . ' detik lalu';
        }

        $diffInMinutes = $created->diffInMinutes($now);
        if ($diffInMinutes < 60) {
            return $diffInMinutes . ' menit lalu';
        }

        $diffInHours = $created->diffInHours($now);
        if ($diffInHours < 24) {
            return $diffInHours . ' jam lalu';
        }

        $diffInDays = $created->diffInDays($now);
        if ($diffInDays < 30) {
            return $diffInDays . ' hari lalu';
        }

        // Untuk bulan dan sisa hari
        $diffInMonths = $created->diffInMonths($now);
        $remainderDays = $created->addMonths($diffInMonths)->diffInDays($now);

        $result = $diffInMonths . ' bulan';
        if ($remainderDays > 0) {
            $result .= ' ' . $remainderDays . ' hari';
        }

        return $result . ' lalu';
    }
    
}
