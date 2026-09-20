@php
    use App\Support\NavMenu;

    $isBackendRoute = request()->routeIs('admin') || request()->routeIs('admin.*');
    $navGroup = $isBackendRoute ? 'backend' : 'frontend';
    $navItems = NavMenu::items($navGroup);
@endphp

<nav class="sticky top-0 z-40 bg-white dark:bg-gray-800 shadow-md">
    <div class="w-full px-4 sm:px-6 lg:px-8 py-3">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center space-x-2 md:space-x-4">
                <a href="{{ route('dashboard') }}">
                    <img 
                        src="{{ asset('images/work-tracker-logo-with-text-425px-horizontal.png') }}" 
                        alt="Work Tracker" 
                        class="h-8 md:h-10 w-auto"
                    />
                </a>
            </div>

            <div class="flex items-center gap-2 md:gap-3 flex-wrap">
                <!-- Sortable menu items: drag to pin an item to a slot, the rest sort by clicks -->
                <div
                    id="nav-sortable"
                    data-nav-group="{{ $navGroup }}"
                    class="flex items-center gap-2 md:gap-3 flex-wrap"
                >
                    @foreach($navItems as $item)
                        <div
                            class="nav-item group relative"
                            data-nav-key="{{ $item['key'] }}"
                            data-nav-pinned="{{ !empty($item['pinned']) ? 'true' : 'false' }}"
                            data-nav-children="{{ !empty($item['children']) ? 'true' : 'false' }}"
                        >
                            @if(!empty($item['children']))
                                <div x-data="{ open: false }" @click.away="open = false">
                                    <button
                                        @click.stop="open = !open"
                                        class="nav-trigger flex items-center gap-1.5 px-3 py-1.5 text-sm rounded transition-colors {{ $item['classes'] }}"
                                    >
                                        @if(!empty($item['image_url']))
                                            <img src="{{ $item['image_url'] }}" alt="{{ $item['label'] }}" class="w-4 h-4 {{ $item['image_classes'] ?? '' }}" />
                                        @endif
                                        {{ $item['label'] }}
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div
                                        x-show="open"
                                        x-transition
                                        class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50"
                                        style="display: none;"
                                    >
                                        <!-- Notch/Arrow pointing up -->
                                        <div class="absolute -top-2 right-4 w-4 h-4 bg-white border-l border-t border-gray-200 transform rotate-45"></div>
                                        <div class="relative bg-white rounded-md">
                                            @foreach($item['children'] as $child)
                                                <a
                                                    href="{{ $child['url'] }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    @click="open = false"
                                                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors first:rounded-t-md last:rounded-b-md"
                                                >
                                                    @if(!empty($child['image_url']))
                                                        <img src="{{ $child['image_url'] }}" alt="{{ $child['label'] }}" class="w-4 h-4 {{ $child['image_classes'] ?? '' }}" />
                                                    @endif
                                                    {{ $child['label'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @elseif(!empty($item['route']))
                                <a
                                    href="{{ route($item['route']) }}"
                                    draggable="false"
                                    title="{{ $item['label'] }}"
                                    class="nav-trigger flex items-center gap-1.5 px-3 py-1.5 text-sm rounded transition-colors {{ $item['classes'] }} {{ request()->routeIs($item['active']) ? ($item['active_classes'] ?? '') : '' }}"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @foreach($item['icon'] ?? [] as $path)
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}" />
                                        @endforeach
                                    </svg>
                                    {{ $item['label'] }}
                                </a>
                            @else
                                <a
                                    href="{{ $item['url'] }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    draggable="false"
                                    class="nav-trigger flex items-center gap-1.5 px-3 py-1.5 text-sm rounded transition-colors {{ $item['classes'] }}"
                                >
                                    @if(!empty($item['image_url']))
                                        <img src="{{ $item['image_url'] }}" alt="{{ $item['label'] }}" class="w-4 h-4 {{ $item['image_classes'] ?? '' }}" />
                                    @endif
                                    {{ $item['label'] }}
                                </a>
                            @endif

                            <button
                                type="button"
                                class="nav-pin absolute -top-1.5 -left-1.5 w-4 h-4 flex items-center justify-center rounded-full bg-gray-900 text-white shadow ring-1 ring-white dark:ring-gray-800 transition-opacity opacity-0 group-hover:opacity-60 hover:!opacity-100"
                                title="Pin to this position"
                                aria-label="Pin {{ $item['label'] }} to this position"
                            >
                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 3v2l1 1v4l3 3v2h-7v5l-1 2-1-2v-5H4v-2l3-3V6l1-1V3h8z" />
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>

                <!-- Dark Mode Toggle -->
                @auth
                <button
                    type="button"
                    onclick="toggleDarkMode()"
                    class="flex items-center gap-1.5 px-3 py-1.5 text-sm bg-gray-600 dark:bg-yellow-600 text-white rounded hover:bg-gray-700 dark:hover:bg-yellow-700 transition-colors"
                    title="Toggle Dark Mode"
                >
                    <svg id="dark-mode-icon" class="w-4 h-4 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg id="light-mode-icon" class="w-4 h-4 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <span id="dark-mode-text" class="hidden sm:inline">Dark</span>
                </button>
                @endauth

                <!-- Back/Front Toggle (for all authenticated users) -->
                @auth
                    <a
                        href="{{ $isBackendRoute ? route('dashboard') : route('admin') }}"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-sm bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors"
                    >
                        @if($isBackendRoute)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Front
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            Back
                        @endif
                    </a>
                @else
                    <a
                        href="{{ route('dashboard') }}"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-sm bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        Back
                    </a>
                @endauth

                <!-- Login (if not authenticated) -->
                @guest
                    <a
                        href="{{ route('login') }}"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Login
                    </a>
                @endguest

                <!-- Logout (if authenticated) -->
                @auth
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button
                            type="submit"
                            class="flex items-center gap-1.5 px-3 py-1.5 text-sm bg-red-600 text-white rounded hover:bg-red-700 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</nav>

@include('layouts.navigation-order')
