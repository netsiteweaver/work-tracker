<x-app-layout>
    <div class="py-12">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @auth
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6" id="kanban-board">
                    @php
                        $statuses = [
                            'new' => ['title' => 'New', 'bgClass' => 'bg-blue-600'],
                            'in_progress' => ['title' => 'In Progress', 'bgClass' => 'bg-yellow-600'],
                            'on_hold' => ['title' => 'On Hold', 'bgClass' => 'bg-orange-600'],
                            'maintenance' => ['title' => 'Maintenance', 'bgClass' => 'bg-purple-600'],
                            'completed' => ['title' => 'Completed', 'bgClass' => 'bg-green-600'],
                            'stopped' => ['title' => 'Stopped', 'bgClass' => 'bg-red-600']
                        ];
                        $patternClasses = [
                            'new' => 'bg-pattern-new',
                            'in_progress' => 'bg-pattern-in_progress',
                            'on_hold' => 'bg-pattern-on_hold',
                            'maintenance' => 'bg-pattern-maintenance',
                            'completed' => 'bg-pattern-completed',
                            'stopped' => 'bg-pattern-stopped'
                        ];
                    @endphp

                    @foreach($statuses as $status => $config)
                        <div class="board-column" data-status="{{ $status }}" data-column-order="{{ $loop->index }}">
                            <div class="rounded-lg overflow-hidden shadow-sm border border-gray-200 {{ $patternClasses[$status] }}">
                                <div class="flex items-center justify-between px-4 py-2 border-b border-gray-200 {{ $config['bgClass'] }} column-header cursor-move" title="Drag to reorder columns">
                                    <h2 class="text-sm font-semibold uppercase tracking-wide text-white">
                                        {{ $config['title'] }}
                                    </h2>
                                    <div class="flex items-center gap-2">
                                        <button 
                                            type="button" 
                                            class="column-toggle-btn text-white/80 hover:text-white transition-colors p-1 rounded hover:bg-white/20" 
                                            onclick="toggleColumnProjects('{{ $status }}', event)"
                                            title="Toggle expand/collapse all projects"
                                        >
                                            <svg class="w-4 h-4 column-toggle-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                        <span class="text-xs font-medium text-white/80">
                                            {{ $projectsByStatus[$status]->count() }}
                                        </span>
                                    </div>
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
        .bg-pattern-maintenance {
            background-color: #faf5ff;
            background-image:
                linear-gradient(rgba(168, 85, 247, 0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(168, 85, 247, 0.08) 1px, transparent 1px);
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
        .project-content {
            transition: max-height 0.3s ease-out, opacity 0.3s ease-out, margin 0.3s ease-out;
            overflow: hidden;
            max-height: 1000px;
        }
        .project-card.collapsed .project-content {
            max-height: 0 !important;
            opacity: 0;
            margin: 0;
            padding: 0;
        }
        .project-card.collapsed .project-chevron {
            transform: rotate(-90deg);
        }
        .board-column {
            transition: transform 0.2s;
        }
        .column-toggle-icon {
            transition: transform 0.3s ease;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const boards = document.querySelectorAll('[id^="board-"]');
            const kanbanBoard = document.getElementById('kanban-board');
            const updateUrl = '{{ route("projects.update-order") }}';
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            // Make columns draggable by dragging the header
            new Sortable(kanbanBoard, {
                animation: 200,
                handle: '.column-header',
                ghostClass: 'ghost-card',
                filter: '.project-card',
                preventOnFilter: false,
                onEnd: function(evt) {
                    // Save column order to localStorage
                    saveColumnOrder();
                }
            });

            // Load saved column order
            loadColumnOrder();

            // Border color mapping
            const statusBorderClasses = {
                'new': 'border-blue-500',
                'in_progress': 'border-yellow-500',
                'on_hold': 'border-orange-500',
                'maintenance': 'border-purple-500',
                'completed': 'border-green-500',
                'stopped': 'border-red-500'
            };

            // Function to update project card border color
            function updateProjectCardColor(card, newStatus) {
                // Remove all status border classes
                Object.values(statusBorderClasses).forEach(borderClass => {
                    card.classList.remove(borderClass);
                });
                
                // Add the new border class
                if (statusBorderClasses[newStatus]) {
                    card.classList.add(statusBorderClasses[newStatus]);
                }
                
                // Update data-status attribute
                card.setAttribute('data-status', newStatus);
            }

            // Make project cards draggable
            boards.forEach(board => {
                new Sortable(board, {
                    group: 'projects',
                    animation: 200,
                    ghostClass: 'ghost-card',
                    onEnd: function(evt) {
                        const projectCard = evt.item;
                        const projectId = projectCard.dataset.projectId;
                        const newStatus = evt.to.closest('.board-column').dataset.status;
                        const oldStatus = evt.from.closest('.board-column').dataset.status;

                        // Update border color immediately when moved to new column
                        if (newStatus !== oldStatus) {
                            updateProjectCardColor(projectCard, newStatus);
                        }

                        if (newStatus !== oldStatus || evt.oldIndex !== evt.newIndex) {
                            updateProjectOrder();
                        }
                    }
                });
            });

            // Collapsible projects functionality
            window.toggleProject = function(header) {
                const card = header.closest('.project-card');
                card.classList.toggle('collapsed');
                
                // Save collapsed state to localStorage
                const projectId = card.dataset.projectId;
                const isCollapsed = card.classList.contains('collapsed');
                const collapsedProjects = JSON.parse(localStorage.getItem('collapsedProjects') || '[]');
                
                if (isCollapsed) {
                    if (!collapsedProjects.includes(projectId)) {
                        collapsedProjects.push(projectId);
                    }
                } else {
                    const index = collapsedProjects.indexOf(projectId);
                    if (index > -1) {
                        collapsedProjects.splice(index, 1);
                    }
                }
                
                localStorage.setItem('collapsedProjects', JSON.stringify(collapsedProjects));
                
                // Update column toggle icon state
                const column = card.closest('.board-column');
                if (column) {
                    const status = column.dataset.status;
                    const board = column.querySelector(`#board-${status}`);
                    const projects = board.querySelectorAll('.project-card');
                    const allCollapsed = projects.length > 0 && Array.from(projects).every(c => c.classList.contains('collapsed'));
                    const icon = column.querySelector('.column-toggle-icon');
                    if (icon) {
                        icon.style.transform = allCollapsed ? 'rotate(-90deg)' : 'rotate(0deg)';
                    }
                }
            };

            // Toggle all projects in a column
            window.toggleColumnProjects = function(status, event) {
                event.stopPropagation(); // Prevent triggering column drag
                const column = document.querySelector(`[data-status="${status}"]`);
                const board = column.querySelector(`#board-${status}`);
                const projects = board.querySelectorAll('.project-card');
                
                if (projects.length === 0) return;
                
                // Check if all are collapsed or all are expanded
                const allCollapsed = Array.from(projects).every(card => card.classList.contains('collapsed'));
                const allExpanded = Array.from(projects).every(card => !card.classList.contains('collapsed'));
                
                // Determine target state: if all collapsed, expand all; if all expanded, collapse all; otherwise expand all
                const shouldCollapse = allExpanded;
                
                const collapsedProjects = JSON.parse(localStorage.getItem('collapsedProjects') || '[]');
                
                projects.forEach(card => {
                    const projectId = card.dataset.projectId;
                    const index = collapsedProjects.indexOf(projectId);
                    
                    if (shouldCollapse) {
                        card.classList.add('collapsed');
                        if (index === -1) {
                            collapsedProjects.push(projectId);
                        }
                    } else {
                        card.classList.remove('collapsed');
                        if (index > -1) {
                            collapsedProjects.splice(index, 1);
                        }
                    }
                });
                
                localStorage.setItem('collapsedProjects', JSON.stringify(collapsedProjects));
                
                // Update icon rotation
                const icon = event.target.closest('.column-header').querySelector('.column-toggle-icon');
                if (icon) {
                    icon.style.transform = shouldCollapse ? 'rotate(-90deg)' : 'rotate(0deg)';
                }
            };

            // Restore collapsed state
            const collapsedProjects = JSON.parse(localStorage.getItem('collapsedProjects') || '[]');
            collapsedProjects.forEach(projectId => {
                const card = document.querySelector(`[data-project-id="${projectId}"]`);
                if (card) {
                    card.classList.add('collapsed');
                }
            });

            // Update column toggle icons based on current state
            document.querySelectorAll('.board-column').forEach(column => {
                const status = column.dataset.status;
                const board = column.querySelector(`#board-${status}`);
                const projects = board.querySelectorAll('.project-card');
                
                if (projects.length > 0) {
                    const allCollapsed = Array.from(projects).every(card => card.classList.contains('collapsed'));
                    const icon = column.querySelector('.column-toggle-icon');
                    if (icon) {
                        icon.style.transform = allCollapsed ? 'rotate(-90deg)' : 'rotate(0deg)';
                    }
                }
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

            function saveColumnOrder() {
                const columns = Array.from(kanbanBoard.children);
                const order = columns.map(col => col.dataset.status);
                localStorage.setItem('columnOrder', JSON.stringify(order));
            }

            function loadColumnOrder() {
                const savedOrder = localStorage.getItem('columnOrder');
                if (savedOrder) {
                    try {
                        const order = JSON.parse(savedOrder);
                        const columns = Array.from(kanbanBoard.children);
                        const columnMap = new Map(columns.map(col => [col.dataset.status, col]));
                        
                        order.forEach(status => {
                            const column = columnMap.get(status);
                            if (column) {
                                kanbanBoard.appendChild(column);
                            }
                        });
                    } catch (e) {
                        console.error('Error loading column order:', e);
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
