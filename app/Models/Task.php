<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Task extends Model
{
    use HasFactory;
    protected $table = 'stage_tasks';

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) \Illuminate\Support\Str::uuid();
        });
        static::deleting(function ($model) {
            if ($model->attachments->count() > 0) {
                foreach ($model->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment->file);
                    $attachment->delete();
                }
            }
        });
    }

    public function isSubmitted($workspace_id){
        $task = WorkspaceTask::whereHas('workspaceStage', fn ($q) => ($q->where('workspace_id', $workspace_id)))
                                ->where('stage_task_id', $this->id)->first();
        if($task) return true;
        return false;
    }

    public function answer($workspace_stage_id){
        $task = WorkspaceTask::where('workspace_stage_id', $workspace_stage_id)->where('stage_task_id', $this->id)->first();
        return $task;
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class, 'stage_id', 'id');
    }
    
    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class, 'stage_task_id', 'id');
    }

    public function wTask($workspace_id){
        $wtask = WorkspaceTask::whereHas('workspaceStage', fn ($q) => ($q->where('workspace_id', $workspace_id)))
                                ->where('stage_task_id', $this->id)->first();
        return $wtask;
    }
}
