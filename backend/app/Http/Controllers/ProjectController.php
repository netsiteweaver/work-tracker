<?php

namespace App\Http\Controllers;

use App\Models\Project;
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
        
        // Group by status for dashboard
        $projectsByStatus = [
            'new' => $projects->where('status', 'new')->values(),
            'in_progress' => $projects->where('status', 'in_progress')->values(),
            'on_hold' => $projects->where('status', 'on_hold')->values(),
            'completed' => $projects->where('status', 'completed')->values(),
            'stopped' => $projects->where('status', 'stopped')->values(),
        ];
        
        return view('dashboard', compact('projectsByStatus'));
    }

    /**
     * Display the admin panel with all projects.
     */
    public function admin()
    {
        $projects = Project::orderBy('created_at', 'desc')->get();
        return view('admin', compact('projects'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Store a newly created project.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'dev_path' => 'nullable|string|max:255',
            'staging_url' => 'nullable|url|max:255',
            'production_url' => 'nullable|url|max:255',
            'status' => 'required|in:new,in_progress,on_hold,completed,stopped',
            'start_date' => 'nullable|date',
            'finish_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Set sort_order to the end
        $maxOrder = Project::where('status', $validated['status'])->max('sort_order') ?? -1;
        $validated['sort_order'] = $maxOrder + 1;

        Project::create($validated);

        return redirect()->route('admin')->with('success', 'Project created successfully!');
    }

    /**
     * Show the form for editing a project.
     */
    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified project.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'dev_path' => 'nullable|string|max:255',
            'staging_url' => 'nullable|url|max:255',
            'production_url' => 'nullable|url|max:255',
            'status' => 'sometimes|required|in:new,in_progress,on_hold,completed,stopped',
            'start_date' => 'nullable|date',
            'finish_date' => 'nullable|date',
        ]);

        $project->update($validated);

        return redirect()->route('admin')->with('success', 'Project updated successfully!');
    }

    /**
     * Remove the specified project.
     */
    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('admin')->with('success', 'Project deleted successfully!');
    }

    /**
     * Update project sort orders (for drag and drop).
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'projects' => 'required|array',
            'projects.*.id' => 'required|exists:projects,id',
            'projects.*.sort_order' => 'required|integer',
            'projects.*.status' => 'sometimes|in:new,in_progress,on_hold,completed,stopped',
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

