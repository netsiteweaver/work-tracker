@props(['project', 'showActions' => false])

@php
    $borderClasses = [
        'new' => 'border-blue-500',
        'in_progress' => 'border-yellow-500',
        'on_hold' => 'border-orange-500',
        'maintenance' => 'border-purple-500',
        'completed' => 'border-green-500',
        'stopped' => 'border-red-500'
    ];
    $borderClass = $borderClasses[$project->status] ?? 'border-gray-500';
    
    // Check if project is past due date (only for non-completed/stopped projects)
    $finishDate = $project->finish_date ? \Carbon\Carbon::parse($project->finish_date) : null;
    $isPastDue = $finishDate && $finishDate->isPast() && !in_array($project->status, ['completed', 'stopped']);
    
    $cardClasses = 'relative bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 mb-3 ' . (auth()->check() ? 'cursor-move' : '') . ' hover:shadow-lg transition-shadow border-l-4 ' . $borderClass;
    if ($isPastDue) {
        $cardClasses .= ' ring-2 ring-red-500 ring-opacity-50';
    }

    $canEditProject = auth()->check() && auth()->user()->canEdit();

    // Lowercased haystack used by the project search box (see admin.blade.php)
    $searchIndex = strtolower(trim(implode(' ', array_filter([
        $project->name,
        $project->description,
        $project->maintenance,
        $project->dev_path,
        $project->staging_url,
        $project->production_url,
        str_replace('_', ' ', $project->status),
    ]))));
@endphp

<div class="{{ $cardClasses }} project-card" data-project-id="{{ $project->id }}" data-status="{{ $project->status }}" data-search="{{ $searchIndex }}">
    @if($canEditProject)
        <a href="{{ route('admin.projects.edit', $project) }}"
           class="project-edit-fab"
           draggable="false"
           title="Edit {{ $project->name }}"
           aria-label="Edit {{ $project->name }}"
           onclick="event.stopPropagation();"
           onmousedown="event.stopPropagation();"
           ontouchstart="event.stopPropagation();">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </a>
    @endif

    <div class="flex justify-between items-start mb-2 cursor-pointer project-header" onclick="toggleProject(this)">
        <div class="flex items-center gap-2 flex-wrap project-title-wrap">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ $project->name }}</h3>
            @if($isPastDue)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200" title="Past due date">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Past Due
                </span>
            @endif
        </div>
        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform project-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </div>

    <div class="project-content">
        @if($project->description)
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-3 line-clamp-2">{{ $project->description }}</p>
        @endif

        <div class="space-y-1 text-xs text-gray-500 dark:text-gray-400">
            @if($project->dev_path)
                <div class="flex items-center">
                    <span class="font-medium mr-1">Dev:</span>
                    @php
                        $devUrl = $project->dev_path;
                        // If it's not already a URL, treat it as a file path
                        if (!filter_var($devUrl, FILTER_VALIDATE_URL)) {
                            $devUrl = 'file://' . $devUrl;
                        }
                    @endphp
                    <a href="{{ $devUrl }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline truncate" onclick="event.stopPropagation();" title="Click to open: {{ $project->dev_path }}">
                        {{ $project->dev_path }}
                    </a>
                </div>
            @endif
        @if($project->staging_url)
            <div class="flex items-center">
                <span class="font-medium mr-1">Staging:</span>
                <a href="{{ $project->staging_url }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline truncate">
                    {{ $project->staging_url }}
                </a>
            </div>
        @endif
        @if($project->production_url)
            <div class="flex items-center">
                <span class="font-medium mr-1">Production:</span>
                <a href="{{ $project->production_url }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline truncate">
                    {{ $project->production_url }}
                </a>
            </div>
        @endif
        @if($project->start_date)
            <div class="flex items-center">
                <span class="font-medium mr-1">Started:</span>
                <span>{{ \Carbon\Carbon::parse($project->start_date)->format('M d, Y') }}</span>
            </div>
        @endif
        @if($project->finish_date)
            <div class="flex items-center">
                <span class="font-medium mr-1">End Date:</span>
                <span class="{{ $isPastDue ? 'text-red-600 dark:text-red-400 font-semibold' : '' }}">
                    {{ $finishDate->format('M d, Y') }}
                    @if($isPastDue)
                        <span class="ml-1" title="Past due date">⚠️</span>
                    @endif
                </span>
            </div>
        @endif
        @if($project->maintenance)
            <div class="flex items-start mt-2">
                <span class="font-medium mr-1">Maintenance:</span>
                <span class="text-gray-700 dark:text-gray-300">{{ $project->maintenance }}</span>
            </div>
        @endif
        </div>
    </div>

    @if($showActions)
        <div class="mt-3 flex justify-end space-x-2">
            @if(auth()->check() && auth()->user()->canEdit())
            <a href="{{ route('admin.projects.edit', $project) }}" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                Edit
            </a>
            @endif
            @if(auth()->check() && (auth()->user()->hasPermission('delete_projects') || auth()->user()->isAdmin()))
            <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this project?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                    Delete
                </button>
            </form>
            @endif
        </div>
    @endif
</div>

@once
@push('styles')
<style>
    /* Quick-edit gear, sitting inside the card just left of the chevron */
    .project-edit-fab {
        position: absolute;
        top: 0.5rem;
        right: 2.5rem;
        z-index: 5;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.25rem;
        height: 1.25rem;
        border-radius: 0.25rem;
        background: none;
        color: #6b7280;
        opacity: 0;
        transform: scale(0.8);
        pointer-events: none;
        transition: opacity 0.15s ease, transform 0.15s ease, color 0.15s ease;
    }
    .project-card:hover .project-edit-fab,
    .project-edit-fab:focus,
    .project-edit-fab:focus-visible {
        opacity: 1;
        transform: scale(1);
        pointer-events: auto;
    }
    .project-edit-fab:hover {
        color: #2563eb;
        transform: scale(1.1);
    }
    .project-edit-fab:focus-visible {
        outline: 2px solid #1d4ed8;
        outline-offset: 2px;
    }
    /* Only reserve room for the gear when it is actually rendered, so a long
       project name cannot slide underneath it. */
    .project-edit-fab ~ .project-header .project-title-wrap {
        padding-right: 2.25rem;
    }
    .project-edit-fab svg {
        width: 1rem;
        height: 1rem;
    }
    .dark .project-edit-fab {
        color: #9ca3af;
    }
    .dark .project-edit-fab:hover {
        color: #60a5fa;
    }
    /* Touch devices have no hover, so keep the icon permanently visible */
    @media (hover: none) {
        .project-edit-fab {
            opacity: 0.9;
            transform: none;
            pointer-events: auto;
        }
    }
</style>
@endpush
@endonce
