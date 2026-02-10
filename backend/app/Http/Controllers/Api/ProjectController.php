<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::orderBy('sort_order')->orderBy('created_at', 'desc')->get();
        return response()->json($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'dev_path' => 'nullable|string|max:255',
            'staging_url' => 'nullable|url|max:255',
            'production_url' => 'nullable|url|max:255',
            'status' => 'required|in:new,in_progress,on_hold,completed,stopped',
            'start_date' => 'nullable|date',
            'finish_date' => 'nullable|date|after_or_equal:start_date',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $project = Project::create($validator->validated());
        return response()->json($project, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $project = Project::findOrFail($id);
        return response()->json($project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $project = Project::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'dev_path' => 'nullable|string|max:255',
            'staging_url' => 'nullable|url|max:255',
            'production_url' => 'nullable|url|max:255',
            'status' => 'sometimes|required|in:new,in_progress,completed,stopped',
            'start_date' => 'nullable|date',
            'finish_date' => 'nullable|date',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $project->update($validator->validated());
        return response()->json($project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $project = Project::findOrFail($id);
        $project->delete();
        return response()->json(null, 204);
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
            'projects.*.status' => 'sometimes|in:new,in_progress,completed,stopped',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        foreach ($request->projects as $projectData) {
            $project = Project::find($projectData['id']);
            $project->sort_order = $projectData['sort_order'];
            if (isset($projectData['status'])) {
                $project->status = $projectData['status'];
            }
            $project->save();
        }

        return response()->json(['message' => 'Order updated successfully']);
    }
}
