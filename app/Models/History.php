<?php

namespace App\Models;

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

    public function content($message, $color){
        $icons = [
            'workspace' => 'briefcase',
            'workspace_approval' => 'checklist',
            'stage' => 'timeline-event',
            'stage_approval' => 'checklist',
            'task' => 'subtask',
        ];
        return [
            'icon' => $icons[$this->type],
            'message' => $message,
            'color' => $color,
        ];
    }
}
