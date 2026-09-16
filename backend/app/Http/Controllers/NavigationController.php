<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Support\NavMenu;
use Illuminate\Http\Request;

class NavigationController extends Controller
{
    /**
     * Record a click on a top menu item, so unpinned items can sort themselves
     * most-used first.
     */
    public function track(Request $request)
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'in:'.implode(',', NavMenu::allKeys())],
        ]);

        $usage = NavMenu::usage(auth()->id());
        $usage[$validated['key']] = ($usage[$validated['key']] ?? 0) + 1;

        Setting::set(NavMenu::USAGE_KEY, $usage, auth()->id());

        return response()->json(['usage' => $usage]);
    }

    /**
     * Replace the pinned slots for the current user. Items left out of the map
     * go back to being ordered by clicks.
     */
    public function pin(Request $request)
    {
        $keys = NavMenu::allKeys();

        $validated = $request->validate([
            'pins' => ['present', 'array'],
            'pins.*' => ['integer', 'min:0', 'max:99'],
        ]);

        $pins = array_intersect_key($validated['pins'], array_flip($keys));

        Setting::set(NavMenu::PINS_KEY, $pins, auth()->id());

        return response()->json(['pins' => $pins]);
    }

    /**
     * Forget every pin and click count for the current user.
     */
    public function reset()
    {
        Setting::set(NavMenu::PINS_KEY, [], auth()->id());
        Setting::set(NavMenu::USAGE_KEY, [], auth()->id());

        return back()->with('success', 'Menu order reset successfully!');
    }
}
