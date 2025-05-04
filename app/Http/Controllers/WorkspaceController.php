<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\TanurController;
use App\Models\Stage;
use App\Models\Workspace;
use App\Models\WorkspaceApproval;
use App\Models\WorkspaceStageApproval;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    protected $tanurapi = null;

    public function __construct()
    {
        $this->tanurapi = new TanurController();
    }

    //index
    public function index()
    {
        $apitanur = new TanurController();
        $data = $apitanur->getAgentDetail(session('agent_id'));
        $agent = (object) $data['data']['agent'] ?? null;
        $workspaces = Workspace::where('agent_id', session('agent_id'))->get();
        $workspace_approvals = WorkspaceApproval::where('approver_id', session('agent_id'))->get();
        $stage_approvals = WorkspaceStageApproval::where('approver_id', session('agent_id'))->get();

        $approvals = $workspace_approvals->merge($stage_approvals)->sortByDesc('created_at');
        
        return view('mobile.workspace.index', compact('workspaces', 'agent', 'approvals'));
    }

    // Show
    public function show($workspace_id)
    {
        $workspace = Workspace::findOrFail($workspace_id);
        $stages = Stage::orderBy('order')->with('tasks')->get();
        return view('mobile.workspace.show', compact('workspace', 'stages'));
    }

    //List
    public function list(Request $request)
    {
        $filter = (object) [
            'q' => $request->get('search', ''),
            'field' => $request->get('field', 'code'),
            'order' => $request->get('order') === 'oldest' ? 'asc' : 'desc',
        ];

        $workspaces = Workspace::where('agent_id', session('agent_id'))
            ->where(function ($query) use ($filter) {
                $query->where('name', 'like', '%' . $filter->q . '%')
                    ->orWhere('description', 'like', '%' . $filter->q . '%');
            })
            ->orderBy($filter->field, $filter->order)
            ->paginate(100);
        return view('mobile.workspace.list', compact('workspaces', 'filter'));
    }

    //Add
    public function add()
    {
        $cities = \App\Models\City::orderBy('nama')->get();
        return view('mobile.workspace.add', compact('cities'));
    }

    //Store
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_type' => 'required|in:umroh,haji',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'pic_name' => 'required|string|max:255',
            'pic_phone' => 'required|string|max:15',
            'pic_email' => 'required|email|max:255',
            'jamaah' => 'required|array',
            'jamaah.*.name' => 'required|string',
            'jamaah.*.phone' => 'nullable|regex:/^[0-9]{10,15}$/',
            'jamaah.*.email' => 'nullable|email',
        ]);

        try {
            $workspace = new Workspace();
            $workspace->agent_id = session('agent_id');
            $workspace->name = $request->name;
            $workspace->description = $request->description;
            $workspace->address = $request->address;
            $workspace->city = $request->city;
            $workspace->postal_code = $request->postal_code;
            $workspace->pic_name = $request->pic_name;
            $workspace->pic_phone = $request->pic_phone;
            $workspace->pic_email = $request->pic_email;
            $workspace->product_type = $request->product_type;
            $workspace->status = '0';
            $workspace->save();

            foreach ($request->jamaah as $jamaah) {
                $workspacePilgrim = new \App\Models\WorkspacePilgrim();
                $workspacePilgrim->workspace_id = $workspace->id;
                $workspacePilgrim->name = $jamaah['name'];
                $workspacePilgrim->phone = $jamaah['phone'];
                $workspacePilgrim->email = $jamaah['email'] ?? null;
                $workspacePilgrim->save();
            }

            //Get Superior Tanur API Agent Detail
            $fetch = $this->tanurapi->getAgentDetail(session('agent_id'));
            $superiors = $fetch['data']['superiors'] ?? null;
            if ($superiors) {
                foreach ($superiors as $superior) {
                    $workspaceApproval = new WorkspaceApproval();
                    $workspaceApproval->workspace_id = $workspace->id;
                    $workspaceApproval->approver_id = $superior['id'];
                    $workspaceApproval->status = '0'; // Pending approval
                    $workspaceApproval->save();
                }
            }
            return redirect()->route('agent.workspace.list')->with('success', 'Workspace created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to create workspace: ' . $e->getMessage());
        }
    }

    //Edit
    public function edit($workspace_id)
    {
        $workspace = Workspace::findOrFail($workspace_id);
        $cities = \App\Models\City::orderBy('nama')->get();
        return view('mobile.workspace.edit', compact('workspace', 'cities'));
    }

    //Update
   public function update(Request $request, $workspace_id)
    {
        $workspace = Workspace::findOrFail($workspace_id);
        if($workspace->agent_id != session('agent_id')){
            return redirect()->back()->with('error', 'Aksi Ilegal, Anda tidak bisa mengubah Workspace orang lain.');
        }
        if($workspace->status != '0'){
            return redirect()->back()->with('error', 'Workspace tidak dapat diubah, status sudah bukan pending.');
        }
        if($workspace->is_approved || $workspace->has_approved){
            return redirect()->back()->with('error', 'Workspace tidak dapat diubah, telah disetujui oleh salah satu atau lebih dari seluruh Superior Level.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_type' => 'required|in:umroh,haji',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'pic_name' => 'required|string|max:255',
            'pic_phone' => 'required|string|max:15',
            'pic_email' => 'required|email|max:255',
            'jamaah' => 'required|array',
            'jamaah.*.name' => 'required|string',
            'jamaah.*.phone' => 'nullable|regex:/^[0-9]{10,15}$/',
            'jamaah.*.email' => 'nullable|email',
        ]);

        try {
            // Update workspace fields
            $workspace->name = $request->name;
            $workspace->description = $request->description;
            $workspace->address = $request->address;
            $workspace->city = $request->city;
            $workspace->postal_code = $request->postal_code;
            $workspace->pic_name = $request->pic_name;
            $workspace->pic_phone = $request->pic_phone;
            $workspace->pic_email = $request->pic_email;
            $workspace->product_type = $request->product_type;
            $workspace->save();

            // Sync pilgrims
            // Hapus semua pilgrims lama
            $workspace->pilgrims()->delete();

            // Tambah ulang berdasarkan input baru
            foreach ($request->jamaah as $jamaahData) {
                $workspace->pilgrims()->create([
                    'name' => $jamaahData['name'],
                    'phone' => $jamaahData['phone'],
                    'email' => $jamaahData['email'],
                ]);
            }

            return redirect()->route('agent.workspace.show', $workspace_id)->with('success', 'Workspace updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update workspace: ' . $e->getMessage());
        }
    }


    //Ajukan
    public function sendApproval()
    {
        try {
            $workspace = Workspace::findOrFail(session('agent_id'));

            // Create a new WorkspaceApproval
            $workspaceApproval = new \App\Models\WorkspaceApproval();
            $workspaceApproval->workspace_id = $workspace->id;
            $workspaceApproval->approver_id = auth()->id(); // Assuming the current user is the approver
            $workspaceApproval->status = 0; // Pending approval
            $workspaceApproval->save();

            // Update the workspace status to 1 (submitted for approval)
            $workspace->status = 1;
            $workspace->save();

            return redirect()->route('agent.workspace.list')->with('success', 'Workspace submitted for approval successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to submit workspace for approval: ' . $e->getMessage());
        }
    }

    //Hapus
    public function destroy($workspace_id)
    {
        try {
            $workspace = Workspace::findOrFail($workspace_id);
            if($workspace->agent_id != session('agent_id')){
                return redirect()->back()->with('error', 'Aksi Ilegal, Anda tidak bisa mengubah Workspace orang lain.');
            }
            if($workspace->status != '0'){
                return redirect()->back()->with('error', 'Workspace tidak dapat dihapus, status sudah bukan pending.');
            }
            if($workspace->is_approved || $workspace->has_approved){
                return redirect()->back()->with('error', 'Workspace tidak dapat dihapus, telah disetujui oleh salah satu atau lebih dari seluruh Superior Level.');
            }
            $workspace->delete();

            return redirect()->route('agent.workspace.list')->with('success', 'Workspace deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete workspace: ' . $e->getMessage());
        }
    }
    
}
