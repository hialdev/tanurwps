<?php

namespace App\Helpers;

class ApiTransformer
{
   public static function transformWorkspace($workspace, $showDataLinked = false, $showDetails = false)
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
         'approvals' => $workspace->count() > 0 ? $workspace->approvals->map(function($approval) use ($showDataLinked) {
                           return self::transformWorkspaceApproval($approval, $showDataLinked);
                        }) : [],
         'details' => $workspace->with('workspaceStages.stage'),
      ];
   }

   public static function transformWorkspaceApproval($approval, $showDataLinked = false) {
      $response = [
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
      ];
      if($showDataLinked) $response["workspace"] = $approval->workspace;
      return $response;
   }

   public static function transformStageApproval($approval) {
      $response = [
         "id" => $approval->id,
         "workspace_stage_id" => $approval->workspace_id,
         "approver_id" => $approval->approver_id,
         "status" => $approval->status,
         "approved_at" => $approval->approved_at,
         "rejected_at" => $approval->rejected_at,
         "reason" => $approval->reason,
         "attachment" => $approval->attachment,
         "created_at" => $approval->created_at,
         "updated_at" => $approval->updated_at,
         "data_stage" => [
            "workspace_stage" => $approval->workspaceStage,
            "stage" => $approval->stage,
         ]
      ];

      return $response;
   }

   public static function transformApproval($approval, $showDataLinked = false)
   {
      $isWorkspace = isset($approval->workspace);
      if ($isWorkspace) {
         return self::transformWorkspaceApproval($approval, $showDataLinked);
      }else{
         return self::transformStageApproval($approval);
      }
   }
}
