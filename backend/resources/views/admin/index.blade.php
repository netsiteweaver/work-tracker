<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Back Office') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @php
                $hasProjectsAccess = auth()->check() && auth()->user()->canEdit();
                $hasSettingsAccess = auth()->check() && auth()->user()->canEdit();
                $hasUsersAccess = auth()->check() && auth()->user()->isAdmin();
                $hasAnyAccess = $hasProjectsAccess || $hasSettingsAccess || $hasUsersAccess;
            @endphp

            @if($hasAnyAccess)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Projects Card (only for users who can edit) -->
                    @if($hasProjectsAccess)
                    <a href="{{ route('admin.projects') }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Projects</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Manage your projects, create new ones, edit existing projects, and organize your work.</p>
                                </div>
                                <div class="ml-4">
                                    <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-4">
                                <span class="text-sm text-blue-600 font-medium">Manage Projects →</span>
                            </div>
                        </div>
                    </a>
                    @endif

                    <!-- Settings Card (only for users who can edit) -->
                    @if($hasSettingsAccess)
                    <a href="{{ route('admin.settings') }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Settings</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Configure dashboard appearance, column visibility, and customize your workspace.</p>
                                </div>
                                <div class="ml-4">
                                    <svg class="w-12 h-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-4">
                                <span class="text-sm text-indigo-600 font-medium">Configure Settings →</span>
                            </div>
                        </div>
                    </a>
                    @endif

                    <!-- Users Card (only for admins) -->
                    @if($hasUsersAccess)
                    <a href="{{ route('admin.users.index') }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Users</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Manage user accounts, assign roles, and control access permissions.</p>
                                </div>
                                <div class="ml-4">
                                    <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-4">
                                <span class="text-sm text-green-600 font-medium">Manage Users →</span>
                            </div>
                        </div>
                    </a>
                    @endif
                </div>
            @else
                <!-- No access message -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No Access</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            You don't have permission to access any admin features. Please contact an administrator if you need access.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Go to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

