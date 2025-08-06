<?php

namespace App\Helpers;

class ApiTransformer
{
   public static function transformWorkspace($workspace)
   {
      return [
         'id' => $workspace->id,
         'name' => $workspace->name,
         'description' => $workspace->description ?? 'tidak ada deskripsi',
         'status' => $workspace->status,
         'status_name' => $workspace->getStatus()['name'],
         'status_color' => $workspace->getStatus()['color'],
         'status_message' => $workspace->getStatus()['message'],
         'live_score' => $workspace->live_score,
         'total_pilgrim_male' => $workspace->total_pilgrim_male,
         'total_pilgrim_female' => $workspace->total_pilgrim_female,
         'total_pilgrim' => $workspace->total_pilgrim_male + $workspace->total_pilgrim_female,
         'stage_analytic' => $workspace->stageAnalytic(),
         'task_analytic' => $workspace->taskAnalytic(),
         'requester' => $workspace->requester,
         'approvals' => $workspace->count() > 0 ? $workspace->approvals->map(function($approval) {
                           return self::transformWorkspaceApproval($approval);
                        }) : [],
      ];
   }

   public static function transformWorkspaceApproval($approval) {
      return [
         "id" => $approval->id,
         "workspace_id" => $approval->workspace_id,
         "approver_id" => $approval->approver_id,
         "status" => $approval->status,
         "approved_at" => $approval->approved_at,
         "rejected_at" => $approval->rejected_at,
         "reason" => $approval->reason,
         "attachment" => $approval->attachment,
         "created_at" => $approval->created_at,
         "updated_at" => $approval->updated_at,
         "approver" => $approval->approver
      ];
   }

   public static function transformApproval($approval)
   {
      $workspace = $approval->workspace ?? $approval->workspaceStage?->workspace;
      $stage = $approval->workspaceStage?->stage;

      return [
         'id' => $approval->id,
         'type' => $approval->workspace ? 'workspace' : 'stage',
         'status' => $approval->status,
         'status_name' => $approval->getStatus()['name'],
         'status_color' => $approval->getStatus()['color'],
         'time_ago' => $approval->time_ago,
         'requester' => [
            'id' => $approval->requester->id,
            'name' => $approval->requester->name,
            'level' => $approval->requester->level,
            'image_url' => $approval->requester->image_url,
         ],
         'workspace' => $workspace ? self::transformWorkspace($workspace) : null,
         'stage_name' => $stage?->name,
         'stage_deadline' => $approval->workspaceStage?->deadlineCount()['message'] ?? null,
      ];
   }
}
