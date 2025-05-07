<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use App\Models\Task;
use App\Models\Workspace;
use App\Models\WorkspaceStage;
use App\Models\WorkspaceTask;
use App\Models\WorkspaceTaskAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WorkspaceTaskController extends Controller
{
    //Show
    public function show($id, $task_id){
        $workspace = Workspace::find($id);
        $task = Task::find($task_id);
        return view('mobile.workspace.task.show', compact('task', 'workspace'));
    }

    //Store
    public function store($id, $task_id, Request $request){
        $task = Task::findOrFail($task_id);
        $workspace = Workspace::findOrFail($id);

        $request->validate([
            'answer_text' => 'required|string',
            'filenames' => 'nullable|array',
            'filenames.*' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,webp|max:5120',
        ]);

        try {
            $wstage = WorkspaceStage::where('workspace_id', $id)->where('stage_id', $task->stage_id)->first();
            if(!$wstage){
                $wstage = new WorkspaceStage();
                $wstage->workspace_id = $id;
                $wstage->stage_id = $task->stage_id;
                $wstage->status = '0';
                $wstage->deadline_at = $wstage->stage->deadlineCount($workspace->approved_at)['deadline_date'];
                $wstage->save();
            }
            
            $wtask = new WorkspaceTask();
            $wtask->workspace_stage_id = $wstage->id;
            $wtask->stage_task_id = $task->id;
            $wtask->answer_text = $request->answer_text;
            $wtask->score = $task->score;
            $wtask->finished_at = now();
            $wtask->status = '1'; // 0: Belum Selesai, 1: Selesai
            $wtask->save();

            if($request->hasFile('attachments')) {
                if($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $index => $attachment) {
                        $customName = null;

                        if (isset($request->filenames[$index])) {
                            $customName = $request->filenames[$index];
                        } else {
                            $customName = $attachment->getClientOriginalName();
                        }

                        $extension = $attachment->getClientOriginalExtension();
                        $filename = time() . '_' . uniqid() . '_' . $customName . '.' . $extension;
                        
                        $path = $attachment->storeAs('workspace/'.session('agent_id').'/task', $filename, 'public');

                        $wtaskAttachment = new WorkspaceTaskAttachment();
                        $wtaskAttachment->workspace_task_id = $wtask->id;
                        $wtaskAttachment->name = $customName;
                        $wtaskAttachment->file = $path;
                        $wtaskAttachment->mime = $attachment->getClientMimeType();
                        $wtaskAttachment->save();
                    }
                }
            }

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyelesaikan Task '.$task->name .': '. $e->getMessage());
        }

        return redirect()->route('agent.workspace.show', $id)->with('success', 'Berhasil menyimpan penyelesaian / jawaban Task : '.$task->name);
    }

    public function update($id, $task_id, $wtask_id, Request $request)
    {
        $task = Task::findOrFail($task_id);
        $workspace = Workspace::findOrFail($id);
        $wtask = WorkspaceTask::findOrFail($wtask_id);

        $request->validate([
            'answer_text' => 'required|string',
            'filenames' => 'nullable|array',
            'filenames.*' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,webp|max:5120',
            'deleted_attachments' => 'nullable|string'
        ]);

        try {
            // Update teks jawaban
            $wtask->answer_text = $request->answer_text;
            $wtask->status = '1'; // selesai
            $wtask->finished_at = now();
            $wtask->save();

            // Hapus lampiran yang ditandai untuk dihapus
            if ($request->filled('deleted_attachments')) {
                $ids = explode(',', $request->deleted_attachments);
                $attachments = WorkspaceTaskAttachment::whereIn('id', $ids)->where('workspace_task_id', $wtask->id)->get();
                foreach ($attachments as $attachment) {
                    Storage::disk('public')->delete($attachment->file);
                    $attachment->delete();
                }
            }

            // Tambah lampiran baru
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $index => $attachment) {
                    $customName = $request->filenames[$index] ?? $attachment->getClientOriginalName();
                    $extension = $attachment->getClientOriginalExtension();
                    $filename = time() . '_' . uniqid() . '_' . $customName . '.' . $extension;

                    $path = $attachment->storeAs('workspace/' . session('agent_id') . '/task', $filename, 'public');

                    $wtaskAttachment = new WorkspaceTaskAttachment();
                    $wtaskAttachment->workspace_task_id = $wtask->id;
                    $wtaskAttachment->name = $customName;
                    $wtaskAttachment->file = $path;
                    $wtaskAttachment->mime = $attachment->getClientMimeType();
                    $wtaskAttachment->save();
                }
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui Task '.$task->name .': '. $e->getMessage());
        }

        return redirect()->route('agent.workspace.show', $workspace->id)->with('success', 'Berhasil memperbarui laporan Task : '.$task->name);
    }
}
