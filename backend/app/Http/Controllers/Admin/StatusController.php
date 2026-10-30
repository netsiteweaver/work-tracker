<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Status;
use App\Support\NavColors;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Back office CRUD for the dashboard groups (App\Models\Status).
 */
class StatusController extends Controller
{
    public function index()
    {
        $this->authorizeEdit();

        $counts = Project::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.statuses.index', [
            'statuses' => Status::ordered(),
            'counts' => $counts,
            'colors' => NavColors::COLORS,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeEdit();

        $validated = $this->validated($request);

        Status::create($validated + [
            'key' => Status::makeKey($validated['label']),
            'sort_order' => (Status::max('sort_order') ?? -1) + 1,
        ]);

        return redirect()->route('admin.statuses.index')->with('success', 'Group created successfully!');
    }

    public function update(Request $request, Status $status)
    {
        $this->authorizeEdit();

        $status->update($this->validated($request));

        return redirect()->route('admin.statuses.index')->with('success', 'Group updated successfully!');
    }

    public function destroy(Status $status)
    {
        $this->authorizeEdit();

        $count = Project::where('status', $status->key)->count();
        if ($count > 0) {
            return redirect()->route('admin.statuses.index')->withErrors([
                'status' => "\"{$status->label}\" still has {$count} project(s). Move them to another group first.",
            ]);
        }

        if (Status::count() <= 1) {
            return redirect()->route('admin.statuses.index')->withErrors([
                'status' => 'At least one group is required.',
            ]);
        }

        $status->delete();

        return redirect()->route('admin.statuses.index')->with('success', 'Group deleted successfully!');
    }

    /**
     * @return array{label: string, color: string}
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'color' => ['required', Rule::in(NavColors::COLORS)],
        ]);
    }

    private function authorizeEdit(): void
    {
        if (! auth()->user()->canEdit()) {
            abort(403, 'You do not have permission to manage groups.');
        }
    }
}
