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
         'total_pilgrim_male' => $workspace->total_pilgrim_male,
         'total_pilgrim_female' => $workspace->total_pilgrim_female,
         'total_pilgrim' => $workspace->total_pilgrim_male + $workspace->total_pilgrim_female,
         'stage_analytic' => $workspace->stageAnalytic(),
         'task_analytic' => $workspace->taskAnalytic(),
         'finished_at' => $workspace->finished_at,
         'approved_at' => $workspace->approved_at,
         'created_at' => $workspace->created_at,
         'updated_at' => $workspace->updated_at,
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

   public static function normalizeWorkspace($workspace, $showDataLinked){
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
         'total_pilgrim_male' => $workspace->total_pilgrim_male,
         'total_pilgrim_female' => $workspace->total_pilgrim_female,
         'total_pilgrim' => $workspace->total_pilgrim_male + $workspace->total_pilgrim_female,
         'stage_analytic' => $workspace->stageAnalytic(),
         'task_analytic' => $workspace->taskAnalytic(),
         'finished_at' => $workspace->finished_at,
         'approved_at' => $workspace->approved_at,
         'created_at' => $workspace->created_at,
         'updated_at' => $workspace->updated_at,
      ];

      if($showDataLinked){
         $response['approvals'] = $workspace->approvals;
      }
      return $response;
   }

   public static function normalizeMinimalWorkspace($workspace){
      $response = [
         'id' => $workspace->id,
         'agent_id' => $workspace->agent_id,
         'name' => $workspace->name,
         'description' => $workspace->description ?? 'tidak ada deskripsi',
         'total_pilgrim_male' => $workspace->total_pilgrim_male,
         'total_pilgrim_female' => $workspace->total_pilgrim_female,
         'total_pilgrim' => $workspace->total_pilgrim_male + $workspace->total_pilgrim_female,
         'stage_analytic' => $workspace->stageAnalytic(),
         'task_analytic' => $workspace->taskAnalytic(),
      ];

      return $response;
   }

   public static function normalizeStage($stage, $showDataLinked, $workspace_id){
      $response = [
         "id" => $stage->id,
         "name" => $stage->name,
         "order" => $stage->order,
         "description" => $stage->description,
         "deadline_days" => $stage->deadline_days,
         "created_at" => $stage->created_at,
         "updated_at" => $stage->updated_at,
         "attachments" => $stage->attachments?->map(fn($attachment) => self::normalizeAttachment($attachment,'stage')),
      ];

      if ($showDataLinked && $workspace_id) $response["tasks"] = $stage->tasks->map(fn($task) => self::normalizeTask($task, $workspace_id));

      return $response;
   }

   public static function normalizeWstage($wstage, $showDataLinked){
      $response = [
         "id" => $wstage->id,
         "workspace_id" => $wstage->workspace_id,
         "stage_id" => $wstage->stage_id,
         "final_score" => $wstage->final_score,
         "deadline_at" => $wstage->deadline_at,
         "status" => $wstage->status,
         "finished_at" => $wstage->finished_at,
         "approved_at" => $wstage->approved_at,
      ];

      if ($showDataLinked) $response["stage"] = self::normalizeStage($wstage->stage, true, $wstage->workspace_id);
      return $response;
   }

   public static function normalizeTask($task, $workspace_id){
      $wtask = $task->wTask($workspace_id);

      $response = [
         "id" => $task->id,
         "stage_id" => $task->stage_id,
         "name" => $task->name,
         "order" => $task->order,
         "description" => $task->description,
         "score" => $task->score,
         "created_at" => $task->created_at,
         "updated_at" => $task->updated_at,
         "attachments" => $task->attachments?->map(fn($task) => self::normalizeAttachment($task, 'stage_task')) ?? [],
         "wtask" => self::normalizeWtask($wtask)
      ];

      return $response;
   }

   public static function normalizeWtask($wtask){
      $response = [
         "id" => $wtask->id,
         "workspace_stage_id" => $wtask->iworkspace_stage_idd,
         "stage_task_id" => $wtask->stage_task_id,
         "score" => $wtask->score,
         "answer_text" => $wtask->answer_text,
         "status" => $wtask->status,
         "finished_at" => $wtask->finished_at,
         "created_at" => $wtask->created_at,
         "updated_at" => $wtask->updated_at,
         "attachments" => $wtask->attachments?->map(fn($attach) => self::normalizeAttachment($attach, 'stage_task')) ?? [],
      ];

      return $response;
   }

   public static function normalizeSapproval($sapproval, $showDataLinked){
      $stage = $sapproval->workspaceStage->stage;
      $workspace = $sapproval->workspaceStage->workspace;

      $response = [
         "id" => $sapproval->id,
         "type" => "stage",
         "workspace_stage_id" => $sapproval->workspace_id,
         "approver_id" => $sapproval->approver_id,
         "status" => $sapproval->status,
         "approved_at" => $sapproval->approved_at,
         "rejected_at" => $sapproval->rejected_at,
         "reason" => $sapproval->reason,
         "attachment" => !$sapproval->attachment ? null : env('APP_URL').'/storage/'.$sapproval->attachment,
         "created_at" => $sapproval->created_at,
         "updated_at" => $sapproval->updated_at,
         
      ];
      
      if ($showDataLinked) {
         $response["stage"] = [
            'name' => $stage->name,
            'deadline_at' => $sapproval->workspaceStage->deadline_at,
            'created_at' => $sapproval->workspaceStage->created_at,
         ];
         $response["workspace"] = self::normalizeMinimalWorkspace($workspace);
      }

      return $response;
   }
   
   public static function normalizeWapproval($wapproval, $showDataLinked, $isMinimal){
      $response =  [
         "id" => $wapproval->id,
         "type" => "workspace",
         "workspace_id" => $wapproval->workspace_id,
         "approver_id" => $wapproval->approver_id,
         "status" => $wapproval->status,
         "approved_at" => $wapproval->approved_at,
         "rejected_at" => $wapproval->rejected_at,
         "reason" => $wapproval->reason,
         "attachment" => !$wapproval->attachment ? null : env('APP_URL').'/storage/'.$wapproval->attachment,
         "created_at" => $wapproval->created_at,
         "updated_at" => $wapproval->updated_at,
      ];

      if ($showDataLinked){
         if ($isMinimal){
            $response["workspace"] = self::normalizeMinimalWorkspace($wapproval->workspace);
         }else{
            $response["workspace"] = self::normalizeWorkspace($wapproval->workspace, $isMinimal);
         }
      }

      return $response;
   }

   public static function normalizeApproval($approval, $showDataLinked, $isMinimal)
   {
      $isWorkspace = isset($approval->workspace);
      if ($isWorkspace) {
         return self::normalizeWapproval($approval, $showDataLinked, $isMinimal);
      }else{
         return self::normalizeSapproval($approval, $showDataLinked);
      }
   }

   public static function normalizeAttachment($attachment,$type){
      $response = [
         "id" => $attachment->id,
         "name" => $attachment->name,
         "file" => !$attachment->file ? null : env('APP_URL').'/storage/'.$attachment->file,
         "mime" => $attachment->mime,
         "created_at" => $attachment->created_at,
         "updated_at" => $attachment->updated_at
      ];

      switch ($type) {
         case 'stage':
            $response["stage_id"] = $attachment->stage_id;
            break;
         case 'stage_task':
            $response["stage_task_id"] = $attachment->stage_task_id;
            break;
         case 'workspace_task':
            $response["workspace_task_id"] = $attachment->workspace_task_id;
            break;
      }

      return $response;
   }

   public static function workspaceDetail($workspace){
      $response = [
         "workspace" => self::normalizeWorkspace($workspace, true),
         "stages" => $workspace->workspaceStages?->map(fn($wstage) => self::normalizeWstage($wstage, true)) ?? [],
      ];
      
      return $response;
   }
}
