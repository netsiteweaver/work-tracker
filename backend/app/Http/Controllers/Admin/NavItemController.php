<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavItem;
use App\Support\NavColors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Back office CRUD for the top menu buttons (App\Models\NavItem).
 */
class NavItemController extends Controller
{
    public function index()
    {
        $this->authorizeEdit();

        return view('admin.nav-items.index', [
            'items' => NavItem::tree(),
            'colors' => NavColors::COLORS,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeEdit();

        $validated = $this->validated($request);

        $item = new NavItem($validated);
        $item->key = NavItem::makeKey($validated['label']);
        $item->sort_order = NavItem::where('parent_id', $validated['parent_id'])->max('sort_order') + 1;

        if ($request->hasFile('image')) {
            $item->image = $request->file('image')->store('nav', 'public');
        }

        $item->save();

        return redirect()->route('admin.nav-items.index')->with('success', 'Menu item created successfully!');
    }

    public function update(Request $request, NavItem $navItem)
    {
        $this->authorizeEdit();

        $validated = $this->validated($request, $navItem);

        // Moving between levels restarts the item at the end of its new list.
        if ($validated['parent_id'] !== $navItem->parent_id) {
            $navItem->sort_order = NavItem::where('parent_id', $validated['parent_id'])->max('sort_order') + 1;
        }

        $navItem->fill($validated);

        if ($request->hasFile('image')) {
            $this->deleteImage($navItem);
            $navItem->image = $request->file('image')->store('nav', 'public');
        } elseif ($request->boolean('remove_image')) {
            $this->deleteImage($navItem);
            $navItem->image = null;
        }

        $navItem->save();

        return redirect()->route('admin.nav-items.index')->with('success', 'Menu item updated successfully!');
    }

    public function destroy(NavItem $navItem)
    {
        $this->authorizeEdit();

        // Children go with the parent (cascade), so clean their images up too.
        foreach ($navItem->children as $child) {
            $this->deleteImage($child);
        }
        $this->deleteImage($navItem);

        $navItem->delete();

        return redirect()->route('admin.nav-items.index')->with('success', 'Menu item deleted successfully!');
    }

    /**
     * Persist the order of a level after a drag & drop.
     */
    public function updateOrder(Request $request)
    {
        $this->authorizeEdit();

        $validated = $request->validate([
            'ids' => ['present', 'array'],
            'ids.*' => ['integer', 'exists:nav_items,id'],
        ]);

        foreach ($validated['ids'] as $position => $id) {
            NavItem::whereKey($id)->update(['sort_order' => $position]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?NavItem $navItem = null): array
    {
        // A top-level item with dropdown entries is just a label that opens
        // them, so it is the one kind of item that carries no link of its own.
        $isDropdownParent = $navItem && $navItem->parent_id === null && $navItem->children()->exists();

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'url' => [Rule::requiredIf(! $isDropdownParent), 'nullable', 'url', 'max:255'],
            'parent_id' => [
                'nullable',
                // The menu is two levels deep, so an item that already has its
                // own dropdown cannot itself move into one.
                Rule::prohibitedIf($isDropdownParent),
                Rule::exists('nav_items', 'id')->whereNull('parent_id'),
            ],
            'color' => ['nullable', Rule::in(NavColors::COLORS)],
            'outline' => ['nullable', 'boolean'],
            'classes' => ['nullable', 'string', 'max:255'],
            'image_classes' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,svg', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
            'remove_image' => ['nullable', 'boolean'],
        ], [
            'parent_id.prohibited' => 'An item with its own dropdown entries cannot be moved into another item.',
            'url.required' => 'A link is required, unless the item has dropdown entries.',
        ]);

        $parentId = $validated['parent_id'] ?? null;
        if ($navItem && (int) $parentId === $navItem->id) {
            $parentId = $navItem->parent_id;
        }

        return [
            'label' => $validated['label'],
            'url' => $validated['url'] ?? null,
            'parent_id' => $parentId ? (int) $parentId : null,
            'color' => $validated['color'] ?? null,
            'outline' => $request->boolean('outline'),
            // A colour drives the styling; the raw string is only kept for an
            // item the palette cannot describe.
            'classes' => empty($validated['color']) ? ($validated['classes'] ?? null) : null,
            'image_classes' => $validated['image_classes'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ];
    }

    private function deleteImage(NavItem $item): void
    {
        if ($item->hasUploadedImage()) {
            Storage::disk('public')->delete($item->image);
        }
    }

    private function authorizeEdit(): void
    {
        if (! auth()->user()->canEdit()) {
            abort(403, 'You do not have permission to manage the top menu.');
        }
    }
}
