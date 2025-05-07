<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkspaceTask extends Model
{
    use HasFactory;
    protected $table = 'workspace_tasks';
    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) \Illuminate\Support\Str::uuid();
        });
    }

    public function workspaceStage(){
        return $this->belongsTo(WorkspaceStage::class, 'workspace_stage_id', 'id');
    }

    public function attachments(){
        return $this->hasMany(WorkspaceTaskAttachment::class, 'workspace_task_id', 'id');
    }
}
