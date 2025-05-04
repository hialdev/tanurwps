<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    //Index
    public function index(Request $request)
    {
        $filter = (object) [
            'q' => $request->get('search', ''),
            'field' => $request->get('field', 'order'),
            'order' => $request->get('order') === 'oldest' ? 'asc' : 'desc',
        ];

        $tasks = Task::where('name', 'like', '%' . $filter->q . '%')
            ->orWhere('description', 'like', '%' . $filter->q . '%')
            ->orderBy($filter->field, $filter->order)
            ->paginate(100);

        return view('tasks.index', compact('tasks', 'filter'));
    }
    //Add
    public function add()
    {
        $stages = Stage::orderBy('name', 'asc')->get();
        return view('tasks.add', compact('stages'));
    }
    
    //Store
    public function store(Request $request)
    {
        $request->validate([
            'stage_id' => 'required|string|exists:stages,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|numeric',
            'score' => 'nullable|numeric',
            'filenames' => 'nullable|array',
            'filenames.*' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,webp|max:5120',
        ]);

        try {
            $task = new Task();
            $task->stage_id = $request->stage_id;
            $task->name = $request->name;
            $task->description = $request->description;
            $task->order = $request->order;
            $task->score = $request->score;
            $task->save();

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
                    
                    Storage::disk('public')->putFileAs('tasks', $attachment, $filename);
                    
                    TaskAttachment::create([
                        'stage_task_id' => $task->id,
                        'file' => 'tasks/' . $filename,
                        'name' => $customName,
                        'mime' => $attachment->getClientMimeType(),
                    ]);
                }
            }

            return redirect()->route('task.index')->with('success', 'Task created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create task: '.$e->getMessage());
        }
    }

    //Edit
    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $stages = Stage::orderBy('name', 'asc')->get();
        return view('tasks.edit', compact('task', 'stages'));
    }

    //Update
    public function update(Request $request, $id)
    {
        $request->validate([
            'stage_id' => 'required|string|exists:stages,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|numeric',
            'score' => 'nullable|numeric',
            'deleted_attachments' => 'nullable', // for deleted attachments
            'filenames' => 'nullable|array',
            'filenames.*' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,webp|max:5120',
        ]);

        try {
            $task = Task::findOrFail($id);
            $task->stage_id = $request->stage_id;
            $task->name = $request->name;
            $task->description = $request->description;
            $task->order = $request->order;
            $task->score = $request->score;
            $task->save();

            // Handle deleted attachments
            if ($request->has('deleted_attachments')) {
                $deletedAttachments = explode(',', $request->deleted_attachments);
                foreach ($deletedAttachments as $attachmentId) {
                    $attachment = TaskAttachment::findOrFail($attachmentId);
                    $this->deleteAttachment($attachment);
                }
            }

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
                    
                    Storage::disk('public')->putFileAs('tasks', $attachment, $filename);
                    
                    TaskAttachment::create([
                        'stage_task_id' => $task->id,
                        'file' => 'tasks/' . $filename,
                        'name' => $customName,
                        'mime' => $attachment->getClientMimeType(),
                    ]);
                }
            }

            return redirect()->route('task.index')->with('success', 'Task updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update task: '.$e->getMessage());
        }
    }

    //Ajukan

    //Delete
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return redirect()->route('task.index')->with('success', 'Task deleted successfully.');
    }

    //DeleteAttachment
    public function attachmentDelete($id, $attachment_id)
    {
        $attachment = TaskAttachment::findOrFail($attachment_id);
        $this->deleteAttachment($attachment);
        return redirect()->back()->with('success', 'Attachment deleted successfully.');
    }

    //DeleteAttachment
    private function deleteAttachment($attachment)
    {
        Storage::disk('public')->delete($attachment->file);
        $attachment->delete();

        return redirect()->back()->with('success', 'Attachment deleted successfully.');
    }

    //Answer

    //AnswerSave
}
