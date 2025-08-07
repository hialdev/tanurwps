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
         'pic_name' => $workspace->pic_name,
         'pic_phone' => $workspace->pic_phone,
         'pic_email' => $workspace->pic_email,
         'city' => $workspace->city,
         'address' => $workspace->address,
         'product_type' => $workspace->product_type,
         'finished_at' => $workspace->finished_at,
         'approved_at' => $workspace->approved_at,
         'total_pilgrim_male' => $workspace->total_pilgrim_male,
         'total_pilgrim_female' => $workspace->total_pilgrim_female,
         'total_pilgrim' => $workspace->total_pilgrim_male + $workspace->total_pilgrim_female,
         'stage_analytic' => $workspace->stageAnalytic(),
         'task_analytic' => $workspace->taskAnalytic(),
         
      ];
      if ($showDataLinked){
         $response['approvals'] = $workspace->count() > 0 ? $workspace->approvals->map(function($approval) use ($showDataLinked) {
                        return self::transformWorkspaceApproval($approval, false);
                     }) : [];
      }
      if($showDetails) {
         $response['workspace_stages'] = $workspace->workspaceStages()->with(['stage.attachments', 'stage.tasks.attachments', 'workspaceTasks.attachments', 'workspaceTasks'])->get();
      }

      return $response;
   }

   public static function transformWorkspaceApproval($approval, $showDataLinked) {
      $response = [
         "id" => $approval->id,
         "type" => "workspace",
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
         "type" => "stage",
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
            "workspace_stage" => $approval->workspaceStage->makeHidden(['workspace', 'stage']),
            "workspace" => self::transformWorkspace($approval->workspaceStage->workspace->makeHidden(['workspaceStages', 'workspaceTasks', 'stage', 'workspace']), false, false),
            "stage" => $approval->workspaceStage->stage,
         ],
      ];
      // dd($approval->workspaceStage->workspace);
      if ($showDataLinked)
         $response["data_stage"]["workspace"] = self::transformWorkspace($approval->workspaceStage->workspace, false, false);

      return $response;
   }

   public static function transformApproval($approval, $showDataLinked)
   {
      $isWorkspace = isset($approval->workspace);
      if ($isWorkspace) {
         return self::transformWorkspaceApproval($approval, true);
      }else{
         return self::transformStageApproval($approval, $showDataLinked);
      }
   }
}
