<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Stage extends Model
{
    use HasFactory;

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

            if ($model->tasks->count() > 0) {
                foreach ($model->tasks as $task) {
                    $task->delete();
                }
            }
        });
    }

    public function attachments()
    {
        return $this->hasMany(StageAttachment::class, 'stage_id', 'id');
    }
    
    public function tasks()
    {
        return $this->hasMany(Task::class, 'stage_id', 'id');
    }
}
