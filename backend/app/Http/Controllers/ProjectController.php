<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    /**
     * Display the dashboard with Kanban boards.
     */
    public function index()
    {
        $projects = Project::orderBy('sort_order')->orderBy('created_at', 'desc')->get();
        
        // Get user settings
        $userId = auth()->id();
        $columnVisibility = Setting::get('column_visibility', [
            'new' => true,
            'in_progress' => true,
            'on_hold' => true,
            'maintenance' => true,
            'completed' => true,
            'stopped' => true,
        ], $userId);
        
        $initialCollapse = Setting::get('initial_collapse', [
            'new' => false,
            'in_progress' => false,
            'on_hold' => false,
            'maintenance' => false,
            'completed' => false,
            'stopped' => false,
        ], $userId);
        
        $dashboardBackground = Setting::get('dashboard_background', '', $userId);
        
        // Group by status for dashboard
        $projectsByStatus = [
            'new' => $projects->where('status', 'new')->values(),
            'in_progress' => $projects->where('status', 'in_progress')->values(),
            'on_hold' => $projects->where('status', 'on_hold')->values(),
            'maintenance' => $projects->where('status', 'maintenance')->values(),
            'completed' => $projects->where('status', 'completed')->values(),
            'stopped' => $projects->where('status', 'stopped')->values(),
        ];
        
        return view('dashboard', compact('projectsByStatus', 'columnVisibility', 'initialCollapse', 'dashboardBackground'));
    }

    /**
     * Display the admin panel with all projects.
     */
    public function admin()
    {
        // Only users who can edit can access the backend project management
        if (!auth()->user()->canEdit()) {
            abort(403, 'You do not have permission to access project management.');
        }
        
        $projects = Project::orderBy('created_at', 'desc')->get();
        return view('admin', compact('projects'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        if (!auth()->user()->canEdit()) {
            abort(403, 'You do not have permission to create projects.');
        }
        return view('projects.create');
    }

    /**
     * Store a newly created project.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->canEdit()) {
            abort(403, 'You do not have permission to create projects.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'maintenance' => 'nullable|string',
            'dev_path' => 'nullable|string|max:255',
            'staging_url' => 'nullable|url|max:255',
            'production_url' => 'nullable|url|max:255',
            'status' => 'required|in:new,in_progress,on_hold,maintenance,completed,stopped',
            'start_date' => 'nullable|date',
            'finish_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Set sort_order to the end
        $maxOrder = Project::where('status', $validated['status'])->max('sort_order') ?? -1;
        $validated['sort_order'] = $maxOrder + 1;

        Project::create($validated);

        return redirect()->route('admin.projects')->with('success', 'Project created successfully!');
    }

    /**
     * Show the form for editing a project.
     */
    public function edit(Project $project)
    {
        if (!auth()->user()->canEdit()) {
            abort(403, 'You do not have permission to edit projects.');
        }
        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified project.
     */
    public function update(Request $request, Project $project)
    {
        if (!auth()->user()->canEdit()) {
            abort(403, 'You do not have permission to update projects.');
        }
        
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'maintenance' => 'nullable|string',
            'dev_path' => 'nullable|string|max:255',
            'staging_url' => 'nullable|url|max:255',
            'production_url' => 'nullable|url|max:255',
            'status' => 'sometimes|required|in:new,in_progress,on_hold,maintenance,completed,stopped',
            'start_date' => 'nullable|date',
            'finish_date' => 'nullable|date',
        ]);

        $project->update($validated);

        return redirect()->route('admin.projects')->with('success', 'Project updated successfully!');
    }

    /**
     * Remove the specified project.
     */
    public function destroy(Project $project)
    {
        if (!auth()->user()->hasPermission('delete_projects') && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to delete projects.');
        }
        
        $project->delete();
        return redirect()->route('admin.projects')->with('success', 'Project deleted successfully!');
    }

    /**
     * Update project sort orders (for drag and drop).
     * Allow all authenticated users to drag & drop on dashboard (viewers included).
     */
    public function updateOrder(Request $request)
    {
        // All authenticated users can drag & drop on dashboard, but only editors/admins can edit in backend
        if (!auth()->check()) {
            return response()->json(['error' => 'You must be logged in to update project order.'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'projects' => 'required|array',
            'projects.*.id' => 'required|exists:projects,id',
            'projects.*.sort_order' => 'required|integer',
            'projects.*.status' => 'sometimes|in:new,in_progress,on_hold,maintenance,completed,stopped',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        foreach ($request->projects as $projectData) {
            $project = Project::find($projectData['id']);
            if ($project) {
                $project->sort_order = $projectData['sort_order'];
                if (isset($projectData['status'])) {
                    $project->status = $projectData['status'];
                }
                $project->save();
            }
        }

        return response()->json(['message' => 'Order updated successfully']);
    }
}

