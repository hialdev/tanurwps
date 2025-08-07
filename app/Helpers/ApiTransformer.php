<?php

namespace App\Helpers;

class ApiTransformer
{
   public static function transformWorkspace($workspace, $showDataLinked, $showDetails)
   {
      $response = [
         'id' => $workspace->id,
         'agent_id' => $workspace->agent_id,
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
         
      ];
      if ($showDataLinked){
         $response['approvals'] = $workspace->count() > 0 ? $workspace->approvals->map(function($approval) use ($showDataLinked) {
                        return self::transformWorkspaceApproval($approval, $showDataLinked);
                     }) : [];
      }
      if($showDetails) {
         $response['details'] = $workspace->with('workspaceStages.stage.attachments')->with('workspaceStages.stage.tasks.attachments')->with(['workspaceStages.workspaceTasks' => function ($q) use ($workspace) {
                                 $q->whereHas('workspaceStage', fn ($q) => $q->where('workspace_id', $workspace->id));
                              }])->get();
      }

      return $response;
   }

   public static function transformWorkspaceApproval($approval, $showDataLinked) {
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
      // dd($showDataLinked);
      if($showDataLinked) $response["workspace"] = self::transformWorkspace($approval->workspace, false, false);
      return $response;
   }

   public static function transformStageApproval($approval, $showDataLinked) {
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
            "stage" => $approval->workspaceStage->stage,
         ],
      ];
      
      if ($showDataLinked)
         $response["data_stage"]["workspace"] = self::transformWorkspace($approval->workspaceStage->workspace, false, false);

      return $response;
   }

   public static function transformApproval($approval, $showDataLinked)
   {
      $isWorkspace = isset($approval->workspace);
      if ($isWorkspace) {
         return self::transformWorkspaceApproval($approval, $showDataLinked);
      }else{
         return self::transformStageApproval($approval, $showDataLinked);
      }
   }
}
