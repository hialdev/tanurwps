<?php

namespace App\Models;

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
}
