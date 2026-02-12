<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        // Only users who can edit can access settings
        if (!auth()->user()->canEdit()) {
            abort(403, 'You do not have permission to access settings.');
        }
        
        $userId = Auth::id();
        
        // Get current settings or defaults
        $settings = [
            'column_visibility' => Setting::get('column_visibility', [
                'new' => true,
                'in_progress' => true,
                'on_hold' => true,
                'maintenance' => true,
                'completed' => true,
                'stopped' => true,
            ], $userId),
            'initial_collapse' => Setting::get('initial_collapse', [
                'new' => false,
                'in_progress' => false,
                'on_hold' => false,
                'maintenance' => false,
                'completed' => false,
                'stopped' => false,
            ], $userId),
            'dashboard_background' => Setting::get('dashboard_background', '', $userId),
            'dark_mode' => Setting::get('dark_mode', false, $userId),
        ];

        return view('settings.index', compact('settings'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        // Only users who can edit can update settings
        if (!auth()->user()->canEdit()) {
            abort(403, 'You do not have permission to update settings.');
        }
        
        $userId = Auth::id();
        
        // Handle JSON requests (for dark mode toggle)
        if ($request->expectsJson() || $request->isJson()) {
            $validated = $request->validate([
                'dark_mode' => 'nullable|boolean',
            ]);
            
            Setting::set('dark_mode', $validated['dark_mode'] ?? false, $userId);
            
            return response()->json(['success' => true, 'dark_mode' => $validated['dark_mode'] ?? false]);
        }
        
        $validated = $request->validate([
            'column_visibility' => 'nullable|array',
            'column_visibility.*' => 'boolean',
            'initial_collapse' => 'nullable|array',
            'initial_collapse.*' => 'boolean',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'dashboard_background' => 'nullable|string|max:255',
            'dark_mode' => 'nullable|boolean',
        ]);

        // Define all possible statuses
        $allStatuses = ['new', 'in_progress', 'on_hold', 'maintenance', 'completed', 'stopped'];
        
        // Process column visibility - unchecked checkboxes won't be in the request
        $columnVisibility = [];
        foreach ($allStatuses as $status) {
            $columnVisibility[$status] = isset($validated['column_visibility'][$status]) && $validated['column_visibility'][$status];
        }
        Setting::set('column_visibility', $columnVisibility, $userId);
        
        // Process initial collapse - unchecked checkboxes won't be in the request
        $initialCollapse = [];
        foreach ($allStatuses as $status) {
            $initialCollapse[$status] = isset($validated['initial_collapse'][$status]) && $validated['initial_collapse'][$status];
        }
        Setting::set('initial_collapse', $initialCollapse, $userId);
        
        // Handle background image upload
        $backgroundValue = $validated['dashboard_background'] ?? '';
        
        if ($request->hasFile('background_image')) {
            // Delete old background image if it exists
            $oldBackground = Setting::get('dashboard_background', '', $userId);
            if ($oldBackground && strpos($oldBackground, 'storage/') !== false) {
                $oldPath = str_replace('storage/', '', $oldBackground);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            
            // Store new image
            $path = $request->file('background_image')->store('backgrounds', 'public');
            $backgroundValue = 'storage/' . $path;
        } elseif (!empty($validated['dashboard_background'])) {
            // If no file uploaded but text input has value, use that
            $backgroundValue = $validated['dashboard_background'];
        }
        
        Setting::set('dashboard_background', $backgroundValue, $userId);
        
        // Save dark mode preference
        Setting::set('dark_mode', $request->has('dark_mode') && $request->dark_mode, $userId);

        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully!');
    }

    /**
     * Remove background image.
     */
    public function removeBackground()
    {
        // Only users who can edit can remove background
        if (!auth()->user()->canEdit()) {
            abort(403, 'You do not have permission to modify settings.');
        }
        
        $userId = Auth::id();
        
        $background = Setting::get('dashboard_background', '', $userId);
        
        if ($background && strpos($background, 'storage/') !== false) {
            $path = str_replace('storage/', '', $background);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
        
        Setting::set('dashboard_background', '', $userId);
        
        return redirect()->route('admin.settings')->with('success', 'Background image removed successfully!');
    }
}
