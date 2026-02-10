<x-app-layout>
    <div class="py-12">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @auth
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6" id="kanban-board">
                    @php
                        $statuses = [
                            'new' => ['title' => 'New', 'bgClass' => 'bg-blue-600'],
                            'in_progress' => ['title' => 'In Progress', 'bgClass' => 'bg-yellow-600'],
                            'on_hold' => ['title' => 'On Hold', 'bgClass' => 'bg-orange-600'],
                            'completed' => ['title' => 'Completed', 'bgClass' => 'bg-green-600'],
                            'stopped' => ['title' => 'Stopped', 'bgClass' => 'bg-red-600']
                        ];
                        $patternClasses = [
                            'new' => 'bg-pattern-new',
                            'in_progress' => 'bg-pattern-in_progress',
                            'on_hold' => 'bg-pattern-on_hold',
                            'completed' => 'bg-pattern-completed',
                            'stopped' => 'bg-pattern-stopped'
                        ];
                    @endphp

                    @foreach($statuses as $status => $config)
                        <div class="board-column" data-status="{{ $status }}">
                            <div class="rounded-lg overflow-hidden shadow-sm border border-gray-200 {{ $patternClasses[$status] }}">
                                <div class="flex items-center justify-between px-4 py-2 border-b border-gray-200 {{ $config['bgClass'] }}">
                                    <h2 class="text-sm font-semibold uppercase tracking-wide text-white">
                                        {{ $config['title'] }}
                                    </h2>
                                    <span class="text-xs font-medium text-white/80">
                                        {{ $projectsByStatus[$status]->count() }}
                                    </span>
                                </div>
                                <div class="p-4 min-h-[200px] space-y-2" id="board-{{ $status }}">
                                    @foreach($projectsByStatus[$status] as $project)
                                        <x-project-card :project="$project" :show-actions="false" />
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <p class="text-gray-600 text-lg mb-4">Please log in to view your projects.</p>
                        <a href="{{ route('login') }}" class="inline-block px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                            Login
                        </a>
                    </div>
                </div>
            @endauth
        </div>
    </div>

    @push('styles')
    <style>
        .bg-pattern-new {
            background-color: #eff6ff;
            background-image:
                linear-gradient(rgba(59, 130, 246, 0.07) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59, 130, 246, 0.07) 1px, transparent 1px);
            background-size: 16px 16px;
        }
        .bg-pattern-in_progress {
            background-color: #fffbeb;
            background-image:
                linear-gradient(rgba(245, 158, 11, 0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(245, 158, 11, 0.08) 1px, transparent 1px);
            background-size: 16px 16px;
        }
        .bg-pattern-on_hold {
            background-color: #fff7ed;
            background-image:
                linear-gradient(rgba(249, 115, 22, 0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(249, 115, 22, 0.08) 1px, transparent 1px);
            background-size: 16px 16px;
        }
        .bg-pattern-completed {
            background-color: #ecfdf3;
            background-image:
                linear-gradient(rgba(34, 197, 94, 0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(34, 197, 94, 0.08) 1px, transparent 1px);
            background-size: 16px 16px;
        }
        .bg-pattern-stopped {
            background-color: #fef2f2;
            background-image:
                linear-gradient(rgba(248, 113, 113, 0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(248, 113, 113, 0.08) 1px, transparent 1px);
            background-size: 16px 16px;
        }
        .ghost-card {
            opacity: 0.5;
            background: #f0f0f0;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const boards = document.querySelectorAll('[id^="board-"]');
            const updateUrl = '{{ route("projects.update-order") }}';
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            boards.forEach(board => {
                new Sortable(board, {
                    group: 'projects',
                    animation: 200,
                    ghostClass: 'ghost-card',
                    onEnd: function(evt) {
                        const projectId = evt.item.dataset.projectId;
                        const newStatus = evt.to.closest('.board-column').dataset.status;
                        const oldStatus = evt.from.closest('.board-column').dataset.status;

                        if (newStatus !== oldStatus || evt.oldIndex !== evt.newIndex) {
                            updateProjectOrder();
                        }
                    }
                });
            });

            function updateProjectOrder() {
                const projects = [];
                document.querySelectorAll('[id^="board-"]').forEach(board => {
                    const status = board.closest('.board-column').dataset.status;
                    Array.from(board.children).forEach((item, index) => {
                        if (item.dataset.projectId) {
                            projects.push({
                                id: item.dataset.projectId,
                                status: status,
                                sort_order: index
                            });
                        }
                    });
                });

                if (projects.length === 0) {
                    console.warn('No projects to update');
                    return;
                }

                fetch(updateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ projects })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Project order updated successfully:', data);
                })
                .catch(err => {
                    console.error('Error updating project order:', err);
                    alert('Failed to update project order. Please refresh the page.');
                    location.reload();
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
