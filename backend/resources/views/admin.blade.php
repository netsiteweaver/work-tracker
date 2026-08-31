<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Project Management') }}
            </h2>
            @if(auth()->check() && auth()->user()->canEdit())
            <a href="{{ route('admin.projects.create') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                + New Project
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($projects->count() > 0)
                        <div class="project-search mb-6">
                            <label for="project-search-input" class="sr-only">Search projects</label>
                            <div class="project-search-field">
                                <svg class="project-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"></path>
                                </svg>
                                <input
                                    type="text"
                                    id="project-search-input"
                                    class="project-search-input"
                                    placeholder="Search projects by name, description, URL or status..."
                                    autocomplete="off"
                                    aria-describedby="project-search-count"
                                >
                                <button type="button" id="project-search-clear" class="project-search-clear" title="Clear search" aria-label="Clear search" hidden>
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <p id="project-search-count" class="project-search-count" role="status" aria-live="polite" hidden></p>
                        </div>

                        <div class="space-y-4" id="project-list">
                            @foreach($projects as $project)
                                <x-project-card :project="$project" :show-actions="true" />
                            @endforeach
                        </div>

                        <div id="project-search-empty" class="text-center py-12" hidden>
                            <p class="text-gray-600 text-lg mb-4">No projects match “<span id="project-search-term"></span>”.</p>
                            <button type="button" id="project-search-reset" class="inline-block px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Clear search
                            </button>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-600 text-lg mb-4">No projects yet. Create your first project!</p>
                            <a href="{{ route('admin.projects.create') }}" class="inline-block px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                Create Project
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .project-search .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }
        .project-search-field {
            position: relative;
            display: flex;
            align-items: center;
        }
        .project-search-icon {
            position: absolute;
            left: 0.75rem;
            width: 1.125rem;
            height: 1.125rem;
            color: #9ca3af;
            pointer-events: none;
        }
        .project-search-input {
            width: 100%;
            padding: 0.55rem 2.5rem 0.55rem 2.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            background-color: #ffffff;
            color: #111827;
            font-size: 0.875rem;
            line-height: 1.25rem;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .project-search-input::placeholder {
            color: #9ca3af;
        }
        .project-search-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }
        .project-search-clear {
            position: absolute;
            right: 0.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 9999px;
            color: #6b7280;
            transition: background-color 0.15s ease, color 0.15s ease;
        }
        .project-search-clear:hover {
            background-color: #f3f4f6;
            color: #111827;
        }
        .project-search-clear[hidden] {
            display: none;
        }
        .project-search-clear svg {
            width: 0.875rem;
            height: 0.875rem;
        }
        .project-search-count {
            margin-top: 0.5rem;
            font-size: 0.75rem;
            color: #6b7280;
        }
        .dark .project-search-input {
            background-color: #1f2937;
            border-color: #4b5563;
            color: #f9fafb;
        }
        .dark .project-search-clear:hover {
            background-color: #374151;
            color: #f9fafb;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        window.__projectsSyncInitial = @json($projectsSyncFingerprint);
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('project-search-input');
            const list = document.getElementById('project-list');
            if (!input || !list) {
                return;
            }

            const clearBtn = document.getElementById('project-search-clear');
            const resetBtn = document.getElementById('project-search-reset');
            const countEl = document.getElementById('project-search-count');
            const emptyEl = document.getElementById('project-search-empty');
            const termEl = document.getElementById('project-search-term');
            const cards = Array.from(list.querySelectorAll('.project-card'));
            const storageKey = 'adminProjectSearch';

            function applyFilter(persist) {
                const query = input.value.trim().toLowerCase();
                const terms = query.split(/\s+/).filter(Boolean);
                let visible = 0;

                cards.forEach(function (card) {
                    const haystack = card.dataset.search || card.textContent.toLowerCase();
                    const matches = terms.every(function (term) {
                        return haystack.indexOf(term) !== -1;
                    });
                    card.style.display = matches ? '' : 'none';
                    if (matches) {
                        visible++;
                    }
                });

                clearBtn.hidden = terms.length === 0;
                countEl.hidden = terms.length === 0;
                if (terms.length > 0) {
                    countEl.textContent = 'Showing ' + visible + ' of ' + cards.length + ' project' + (cards.length === 1 ? '' : 's');
                }

                const noResults = terms.length > 0 && visible === 0;
                emptyEl.hidden = !noResults;
                if (noResults) {
                    termEl.textContent = input.value.trim();
                }

                if (persist !== false) {
                    try {
                        if (query) {
                            sessionStorage.setItem(storageKey, input.value);
                        } else {
                            sessionStorage.removeItem(storageKey);
                        }
                    } catch (e) {
                        // Storage unavailable (private mode) - filtering still works
                    }
                }
            }

            function clearSearch() {
                input.value = '';
                applyFilter();
                input.focus();
            }

            input.addEventListener('input', function () {
                applyFilter();
            });

            input.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && input.value !== '') {
                    event.preventDefault();
                    clearSearch();
                }
            });

            clearBtn.addEventListener('click', clearSearch);
            if (resetBtn) {
                resetBtn.addEventListener('click', clearSearch);
            }

            // "/" focuses the search box, unless the user is already typing somewhere
            document.addEventListener('keydown', function (event) {
                if (event.key !== '/' || event.ctrlKey || event.metaKey || event.altKey) {
                    return;
                }
                const tag = (event.target.tagName || '').toLowerCase();
                if (tag === 'input' || tag === 'textarea' || tag === 'select' || event.target.isContentEditable) {
                    return;
                }
                event.preventDefault();
                input.focus();
                input.select();
            });

            // Restore the query after the dashboard sync poller reloads the page
            try {
                const saved = sessionStorage.getItem(storageKey);
                if (saved) {
                    input.value = saved;
                }
            } catch (e) {
                // ignore
            }
            applyFilter(false);
        });
    </script>
    @endpush
</x-app-layout>
