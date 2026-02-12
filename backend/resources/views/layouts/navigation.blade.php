<nav class="sticky top-0 z-40 bg-white shadow-md" x-data="{ showServerDropdown: false }">
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

            <div class="flex items-center space-x-2 md:space-x-3 flex-wrap">
                @php
                    $isBackendRoute = request()->routeIs('admin') || request()->routeIs('admin.*');
                @endphp

                @if(!$isBackendRoute)
                    <!-- Frontend Navigation - Service Links -->
                    <!-- Tweezzo -->
                    <a
                        href="https://app.tweezzo.online/users/signin"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-sm bg-cyan-600 text-white rounded hover:bg-cyan-700 transition-colors"
                    >
                        <img src="{{ asset('images/tweezzo-64px.png') }}" alt="Tweezzo" class="w-4 h-4" />
                        Tweezzo
                    </a>

                    <!-- phpMyAdmin -->
                    <a
                        href="http://localhost/phpmyadmin"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-sm bg-teal-600 text-white rounded hover:bg-teal-700 transition-colors"
                    >
                        <img src="{{ asset('images/phpmyadmin-64px.png') }}" alt="phpMyAdmin" class="w-4 h-4" />
                        phpMyAdmin
                    </a>

                    <!-- Server Dropdown -->
                    <div class="relative" x-ref="serverDropdown" @click.away="showServerDropdown = false">
                        <button
                            @click.stop="showServerDropdown = !showServerDropdown"
                            class="flex items-center gap-1.5 px-3 py-1.5 text-sm bg-green-400 text-white rounded hover:bg-green-500 transition-colors"
                        >
                            <img src="{{ asset('images/servers-64px.png') }}" alt="Server" class="w-4 h-4" />
                            Server
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div
                            x-show="showServerDropdown"
                            x-transition
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50"
                            style="display: none;"
                        >
                            <!-- Notch/Arrow pointing up -->
                            <div class="absolute -top-2 right-4 w-4 h-4 bg-white border-l border-t border-gray-200 transform rotate-45"></div>
                            <div class="relative bg-white rounded-md">
                                <a
                                    href="https://my.hosting.com/login"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    @click="showServerDropdown = false"
                                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors first:rounded-t-md last:rounded-b-md"
                                >
                                    <img src="{{ asset('images/hosting.com-64px.png') }}" alt="Hosting.com" class="w-4 h-4" />
                                    Hosting.com
                                </a>
                                <a
                                    href="https://my.contabo.com/account/login"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    @click="showServerDropdown = false"
                                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors first:rounded-t-md last:rounded-b-md"
                                >
                                    <img src="{{ asset('images/contabo-64px.png') }}" alt="Contabo.com" class="w-4 h-4" />
                                    Contabo.com
                                </a>
                                <a
                                    href="https://my.cloud.mu/index.php?rp=/login"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    @click="showServerDropdown = false"
                                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors first:rounded-t-md last:rounded-b-md"
                                >
                                    <img src="{{ asset('images/cloud.mu-64px.png') }}" alt="Cloud.mu" class="w-4 h-4" />
                                    Cloud.mu
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- HMS -->
                    <a
                        href="https://hms.netsiteweaver.com"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors"
                    >
                        <img src="{{ asset('images/hms-64px.png') }}" alt="HMS" class="w-4 h-4" />
                        HMS
                    </a>

                    <!-- GitHub -->
                    <a
                        href="https://github.com"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-sm border-2 border-gray-800 text-gray-800 rounded hover:bg-gray-800 hover:text-white transition-colors"
                    >
                        <img src="{{ asset('images/github-64px.png') }}" alt="GitHub" class="w-4 h-4" />
                        GitHub
                    </a>

                    <!-- GitLab -->
                    <a
                        href="https://gitlab.com"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-sm bg-orange-600 text-white rounded hover:bg-orange-700 transition-colors"
                    >
                        <img src="{{ asset('images/gitlab-64px.png') }}" alt="GitLab" class="w-4 h-4" />
                        GitLab
                    </a>

                    <!-- WhatsApp -->
                    <a
                        href="https://web.whatsapp.com/"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-sm bg-green-500 text-white rounded hover:bg-green-600 transition-colors"
                    >
                        <img src="{{ asset('images/whatsapp-64px.png') }}" alt="WhatsApp" class="w-4 h-4" />
                        WhatsApp
                    </a>
                @else
                    <!-- Backend Navigation - Only Projects and Settings -->
                    @auth
                        <!-- Projects -->
                        <a
                            href="{{ route('admin.projects') }}"
                            class="flex items-center gap-1.5 px-3 py-1.5 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors {{ request()->routeIs('admin.projects*') ? 'ring-2 ring-blue-300' : '' }}"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Projects
                        </a>

                        <!-- Settings -->
                        <a
                            href="{{ route('admin.settings') }}"
                            class="flex items-center gap-1.5 px-3 py-1.5 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('admin.settings*') ? 'ring-2 ring-indigo-300' : '' }}"
                            title="Settings"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Settings
                        </a>
                    @endauth
                @endif

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
                @endif

                <!-- Back/Front Toggle (if authenticated) -->
                @auth
                    <a
                        href="{{ request()->routeIs('admin') || request()->routeIs('admin.*') ? route('dashboard') : route('admin') }}"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-sm bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors"
                    >
                        @if(request()->routeIs('admin') || request()->routeIs('admin.*'))
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
            </div>
        </div>
    </div>
</nav>
