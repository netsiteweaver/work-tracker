@php
    $payload = [
        'id' => $item->id,
        'label' => $item->label,
        'url' => $item->url,
        'parent_id' => $item->parent_id,
        'classes' => $item->classes,
        'image_classes' => $item->image_classes,
        'is_active' => $item->is_active,
        'image_url' => $item->image_url,
        'has_children' => $item->parent_id === null && $item->children->isNotEmpty(),
    ];
@endphp

<div class="flex items-center gap-3 p-3">
    <span class="nav-handle cursor-move text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" title="Drag to reorder">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
        </svg>
    </span>

    <span class="flex items-center gap-1.5 px-3 py-1.5 text-sm rounded {{ $item->classes ?: 'bg-gray-200 text-gray-800' }}">
        @if($item->image_url)
            <img src="{{ $item->image_url }}" alt="" class="w-4 h-4 {{ $item->image_classes }}">
        @endif
        {{ $item->label }}
    </span>

    <div class="min-w-0 flex-1">
        @if($item->url)
            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $item->url }}</p>
        @else
            <p class="text-xs text-gray-400 dark:text-gray-500 italic">Dropdown</p>
        @endif
    </div>

    @unless($item->is_active)
        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">Hidden</span>
    @endunless

    <button type="button" onclick='openNavModal(@json($payload))'
        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 text-sm">
        Edit
    </button>

    <form action="{{ route('admin.nav-items.destroy', $item) }}" method="POST" class="inline"
        onsubmit="return confirm('{{ $payload['has_children'] ? 'Delete this item and its dropdown entries?' : 'Delete this menu item?' }}')">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 text-sm">Delete</button>
    </form>
</div>
