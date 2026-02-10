@props(['project', 'showActions' => false])

@php
    $borderClasses = [
        'new' => 'border-blue-500',
        'in_progress' => 'border-yellow-500',
        'on_hold' => 'border-orange-500',
        'completed' => 'border-green-500',
        'stopped' => 'border-red-500'
    ];
    $badgeClasses = [
        'new' => 'bg-blue-100 text-blue-800',
        'in_progress' => 'bg-yellow-100 text-yellow-800',
        'on_hold' => 'bg-orange-100 text-orange-800',
        'completed' => 'bg-green-100 text-green-800',
        'stopped' => 'bg-red-100 text-red-800'
    ];
    $borderClass = $borderClasses[$project->status] ?? 'border-gray-500';
    $badgeClass = $badgeClasses[$project->status] ?? 'bg-gray-100 text-gray-800';
@endphp

<div class="bg-white rounded-lg shadow-md p-4 mb-3 cursor-move hover:shadow-lg transition-shadow border-l-4 {{ $borderClass }}" data-project-id="{{ $project->id }}">
    <div class="flex justify-between items-start mb-2">
        <h3 class="text-lg font-semibold text-gray-800">{{ $project->name }}</h3>
        <span class="text-xs px-2 py-1 rounded-full {{ $badgeClass }}">
            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
        </span>
    </div>

    @if($project->description)
        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $project->description }}</p>
    @endif

    <div class="space-y-1 text-xs text-gray-500">
        @if($project->dev_path)
            <div class="flex items-center">
                <span class="font-medium mr-1">Dev:</span>
                <span class="truncate">{{ $project->dev_path }}</span>
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

