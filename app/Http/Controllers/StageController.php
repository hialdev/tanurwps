<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use App\Models\StageAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StageController extends Controller
{
    //Index
    public function index(Request $request)
    {
        $filter = (object) [
            'q' => $request->get('search', ''),
            'field' => $request->get('field', 'order'),
            'order' => $request->get('order') === 'oldest' ? 'asc' : 'desc',
        ];
        $stages = Stage::where('name', 'like', '%' . $filter->q . '%')
            ->orWhere('description', 'like', '%' . $filter->q . '%')
            ->orderBy($filter->field, $filter->order)
            ->paginate(100);

        return view('stages.index', compact('stages', 'filter'));
    }

    //Add
    public function add()
    {
        return view('stages.add');
    }

    //Store
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|numeric',
            'deadline_days' => 'required|numeric',
            'filenames' => 'nullable|array',
            'filenames.*' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,webp|max:5120',
        ]);

        try {
            $stage = new Stage();
            $stage->name = $request->name;
            $stage->order = $request->order;
            $stage->deadline_days = $request->deadline_days;
            $stage->description = $request->description;
            $stage->save();

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
                        
                        $path = $attachment->storeAs('stages', $filename, 'public');

                        $stageAttachment = new StageAttachment();
                        $stageAttachment->stage_id = $stage->id;
                        $stageAttachment->name = $customName;
                        $stageAttachment->file = $path;
                        $stageAttachment->mime = $attachment->getClientMimeType();
                        $stageAttachment->save();
                    }
                }

            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create stage: ' . $e->getMessage());
        }

        return redirect()->route('stage.index')->with('success', 'Stage created successfully.');
    }

    //Edit
    public function edit($id)
    {
        $stage = Stage::findOrFail($id);
        return view('stages.edit', compact('stage'));
    }

    //Update
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|numeric',
            'deadline_days' => 'required|numeric',
            'deleted_attachments' => 'nullable', // for deleted attachments
            'filenames' => 'nullable|array', // add this for new filenames
            'filenames.*' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,webp|max:5120',
        ]);


        try {
            $stage = Stage::findOrFail($id);
            $stage->name = $request->name;
            $stage->order = $request->order;
            $stage->deadline_days = $request->deadline_days;
            $stage->description = $request->description;
            $stage->save();

            // Delete removed attachments
            if ($stage->attachments && $request->has('deleted_attachments')) {
                $deleted_attachments = explode(',', $request->deleted_attachments);
                foreach ($deleted_attachments as $attachment_id) {
                    $attachment = StageAttachment::findOrFail($attachment_id);
                    $this->deleteAttachment($attachment);
                }
            }

            // Add new attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $index => $attachment) {
                    $customName = $request->filenames[$index] ?? $attachment->getClientOriginalName();
                    $extension = $attachment->getClientOriginalExtension();
                    $filename = time() . '_' . uniqid() . '_' . $customName . '.' . $extension;

                    $path = $attachment->storeAs('stages', $filename, 'public');

                    $stageAttachment = new StageAttachment();
                    $stageAttachment->stage_id = $stage->id;
                    $stageAttachment->name = $customName;
                    $stageAttachment->file = $path;
                    $stageAttachment->mime = $attachment->getClientMimeType();
                    $stageAttachment->save();
                }
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update stage: ' . $e->getMessage());
        }

        return redirect()->route('stage.index')->with('success', 'Stage updated successfully.');
    }


    //Delete
    public function destroy($id)
    {
        $stage = Stage::findOrFail($id);
        $stage->delete();

        return redirect()->route('stage.index')->with('success', 'Stage deleted successfully.');
    }

    //Ajukan

    //Attachment Delete
    public function attachmentDelete($id, $attachment_id)
    {
        $attachment = StageAttachment::findOrFail($attachment_id);
        $this->deleteAttachment($attachment);
        return redirect()->back()->with('success', 'Attachment deleted successfully.');
    }

    private function deleteAttachment($attachment)
    {
        if (Storage::disk('public')->exists($attachment->file)) {
            Storage::disk('public')->delete($attachment->file);
        }
        $attachment->delete();
    }
}
