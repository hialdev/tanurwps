<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\TanurController;
use App\Models\Workspace;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    //index
    public function index($id)
    {
        $apitanur = new TanurController();
        $data = $apitanur->getAgentDetail($id);
        $agent = (object) $data['data']['agent'] ?? null;
        $workspaces = Workspace::where('agent_id', $id)->first();
        return view('mobile.workspace.index', compact('workspaces', 'agent'));
    }

    // Show
    public function show($id, $workspace_id)
    {
        $workspace = Workspace::findOrFail($workspace_id);
        return view('mobile.workspace.show', compact('workspace'));
    }

    //List
    public function list(Request $request)
    {
        $filter = (object) [
            'q' => $request->get('search', ''),
            'field' => $request->get('field', 'order'),
            'order' => $request->get('order') === 'oldest' ? 'asc' : 'desc',
        ];

        $workspaces = Workspace::where('name', 'like', '%' . $filter->q . '%')
            ->orWhere('description', 'like', '%' . $filter->q . '%')
            ->orderBy($filter->field, $filter->order)
            ->paginate(100);

        return view('mobile.workspace.list', compact('workspaces', 'filter'));
    }

    //Add
    public function add()
    {
        return view('mobile.workspace.add');
    }

    //Store
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'area' => 'required|string|max:255',
            'address1' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'sub_district' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'pic_name' => 'required|string|max:255',
            'pic_phone' => 'required|string|max:15',
            'pic_email' => 'required|email|max:255',
            'product_type' => 'required|in:umroh,haji',
        ]);

        try {
            $workspace = new Workspace();
            $workspace->name = $request->name;
            $workspace->description = $request->description;
            $workspace->area = $request->area;
            $workspace->address1 = $request->address1;
            $workspace->address2 = $request->address2;
            $workspace->city = $request->city;
            $workspace->district = $request->district;
            $workspace->sub_district = $request->sub_district;
            $workspace->postal_code = $request->postal_code;
            $workspace->pic_name = $request->pic_name;
            $workspace->pic_phone = $request->pic_phone;
            $workspace->pic_email = $request->pic_email;
            $workspace->product_type = $request->product_type;
            $workspace->save();

            return redirect()->route('workspace.index')->with('success', 'Workspace created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create workspace: ' . $e->getMessage());
        }
    }

    //Edit
    public function edit($id)
    {
        $workspace = Workspace::findOrFail($id);
        return view('mobile.workspace.edit', compact('workspace'));
    }

    //Update
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'area' => 'required|string|max:255',
            'address1' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'sub_district' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'pic_name' => 'required|string|max:255',
            'pic_phone' => 'required|string|max:15',
            'pic_email' => 'required|email|max:255',
            'product_type' => 'required|in:umroh,haji',
        ]);

        try {
            $workspace = Workspace::findOrFail($id);
            $workspace->name = $request->name;
            $workspace->description = $request->description;
            $workspace->area = $request->area;
            $workspace->address1 = $request->address1;
            $workspace->address2 = $request->address2;
            $workspace->city = $request->city;
            $workspace->district = $request->district;
            $workspace->sub_district = $request->sub_district;
            $workspace->postal_code = $request->postal_code;
            $workspace->pic_name = $request->pic_name;
            $workspace->pic_phone = $request->pic_phone;
            $workspace->pic_email = $request->pic_email;
            $workspace->product_type = $request->product_type;
            $workspace->save();

            return redirect()->route('workspace.index')->with('success', 'Workspace updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update workspace: ' . $e->getMessage());
        }
    }

    //Ajukan
    public function sendApproval($id)
    {
        try {
            $workspace = Workspace::findOrFail($id);

            // Create a new WorkspaceApproval
            $workspaceApproval = new \App\Models\WorkspaceApproval();
            $workspaceApproval->workspace_id = $workspace->id;
            $workspaceApproval->approver_id = auth()->id(); // Assuming the current user is the approver
            $workspaceApproval->status = 0; // Pending approval
            $workspaceApproval->save();

            // Update the workspace status to 1 (submitted for approval)
            $workspace->status = 1;
            $workspace->save();

            return redirect()->route('workspace.index')->with('success', 'Workspace submitted for approval successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to submit workspace for approval: ' . $e->getMessage());
        }
    }

    //Hapus
    public function destroy($id)
    {
        try {
            $workspace = Workspace::findOrFail($id);
            $workspace->delete();

            return redirect()->route('workspace.index')->with('success', 'Workspace deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete workspace: ' . $e->getMessage());
        }
    }
    
}
