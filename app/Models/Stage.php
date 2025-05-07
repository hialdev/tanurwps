<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function deadlineCount($approved_at)
    {
        $deadline_days = $this->deadline_days;
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

    public function isFilled($workspace_id){
        $wstage = WorkspaceStage::where('workspace_id', $workspace_id)->where('stage_id', $this->id)->first();
        if ($wstage) {
            $tasks = $wstage->workspaceTasks;
            if ($tasks->count() > 0) {

                if ($tasks->count() != $this->tasks->count()) return false;

                foreach ($tasks as $task) {
                    if ($task->finished_at == null) {
                        return false;
                    }
                }
                return true;
            }
        }
    }

    public function wstage($workspace_id){
        return WorkspaceStage::where('workspace_id', $workspace_id)->where('stage_id', $this->id)->first();
    }

    public function attachments()
    {
        return $this->hasMany(StageAttachment::class, 'stage_id', 'id');
    }
    
    public function tasks()
    {
        return $this->hasMany(Task::class, 'stage_id', 'id')->orderBy('order', 'asc');
    }
}
