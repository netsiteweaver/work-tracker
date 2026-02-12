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
@endphp

<div class="bg-white rounded-lg shadow-md p-4 mb-3 cursor-move hover:shadow-lg transition-shadow border-l-4 {{ $borderClass }} project-card" data-project-id="{{ $project->id }}">
    <div class="flex justify-between items-start mb-2 cursor-pointer project-header" onclick="toggleProject(this)">
        <h3 class="text-lg font-semibold text-gray-800">{{ $project->name }}</h3>
        <svg class="w-4 h-4 text-gray-500 transition-transform project-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </div>

    <div class="project-content">
        @if($project->description)
            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $project->description }}</p>
        @endif

        <div class="space-y-1 text-xs text-gray-500">
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
                <span class="font-medium mr-1">Finished:</span>
                <span>{{ \Carbon\Carbon::parse($project->finish_date)->format('M d, Y') }}</span>
            </div>
        @endif
        @if($project->maintenance)
            <div class="flex items-start mt-2">
                <span class="font-medium mr-1">Maintenance:</span>
                <span class="text-gray-700">{{ $project->maintenance }}</span>
            </div>
        @endif
        </div>
    </div>

    @if($showActions)
        <div class="mt-3 flex justify-end space-x-2">
            <a href="{{ route('projects.edit', $project) }}" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                Edit
            </a>
            <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this project?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                    Delete
                </button>
            </form>
        </div>
    @endif
</div>

