<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;
    protected $table = 'histories';
    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) \Illuminate\Support\Str::uuid();
        });
    }

    public function relation(){
        $type = $this->type;
        switch ($type) {
            case 'workspace':
                return Workspace::findOrFail($this->relation_id);
                
                break;
            case 'workspace_approval':
                return WorkspaceApproval::findOrFail($this->relation_id);
                
                break;
            case 'stage':
                return WorkspaceStage::findOrFail($this->relation_id);
                
                break;
            case 'stage_approval':
                return WorkspaceStageApproval::findOrFail($this->relation_id);
                
                break;
            case 'task':
                return WorkspaceTask::findOrFail($this->relation_id);
                break;
            
            default:
                return null;
                break;
        }

        return null;
    }

    public function getIconAttribute(){
        $icons = [
            'workspace' => 'briefcase',
            'workspace_approval' => 'checklist',
            'stage' => 'timeline-event',
            'stage_approval' => 'checklist',
            'task' => 'subtask',
        ];
        return $icons[$this->type];
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
