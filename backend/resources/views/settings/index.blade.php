<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Column Visibility -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Column Visibility</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Choose which columns to show or hide on the dashboard.</p>
                        
                        <div class="space-y-3">
                            @php
                                $statuses = [
                                    'new' => 'New',
                                    'in_progress' => 'In Progress',
                                    'on_hold' => 'On Hold',
                                    'maintenance' => 'Maintenance',
                                    'completed' => 'Completed',
                                    'stopped' => 'Stopped'
                                ];
                            @endphp
                            
                            @foreach($statuses as $key => $label)
                                <div class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        id="column_visibility_{{ $key }}" 
                                        name="column_visibility[{{ $key }}]" 
                                        value="1"
                                        {{ ($settings['column_visibility'][$key] ?? true) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    >
                                    <label for="column_visibility_{{ $key }}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $label }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Initial Collapse State -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Initial Collapse State</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Set which columns should be collapsed by default when the dashboard loads.</p>
                        
                        <div class="space-y-3">
                            @foreach($statuses as $key => $label)
                                <div class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        id="initial_collapse_{{ $key }}" 
                                        name="initial_collapse[{{ $key }}]" 
                                        value="1"
                                        {{ ($settings['initial_collapse'][$key] ?? false) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    >
                                    <label for="initial_collapse_{{ $key }}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $label }} (collapsed by default)
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Dark Mode -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Dark Mode</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Enable dark mode for a better viewing experience in low-light conditions.</p>
                        
                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                id="dark_mode" 
                                name="dark_mode" 
                                value="1"
                                {{ ($settings['dark_mode'] ?? false) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600"
                            >
                            <label for="dark_mode" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                Enable Dark Mode
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Background -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Dashboard Background</h3>
                        <p class="text-sm text-gray-600 mb-4">Set a background color, upload an image, or use an image URL for the dashboard.</p>
                        
                        <div class="space-y-4">
                            <!-- Current Background Preview -->
                            @if($settings['dashboard_background'] && strpos($settings['dashboard_background'], 'storage/') !== false)
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Current Background:</p>
                                    <div class="relative inline-block">
                                        <img 
                                            src="{{ asset($settings['dashboard_background']) }}" 
                                            alt="Current background" 
                                            class="max-w-xs max-h-32 rounded border border-gray-300"
                                        />
                                        <a 
                                            href="{{ route('admin.settings.remove-background') }}" 
                                            class="absolute top-0 right-0 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                                            onclick="return confirm('Are you sure you want to remove the background image?')"
                                            title="Remove background image"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endif

                            <!-- Upload Image -->
                            <div>
                                <x-input-label for="background_image" :value="__('Upload Background Image')" />
                                <input 
                                    id="background_image" 
                                    name="background_image" 
                                    type="file" 
                                    accept="image/*"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                />
                                <p class="mt-2 text-xs text-gray-500">Upload an image file (JPG, PNG, GIF, etc.)</p>
                                @error('background_image')
                                    <x-input-error class="mt-2" :messages="[$message]" />
                                @enderror
                            </div>

                            <!-- Or Enter URL/Color -->
                            <div class="border-t dark:border-gray-700 pt-4">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Or enter a color or image URL:</p>
                                <x-input-label for="dashboard_background" :value="__('Background Color or URL')" />
                                <x-text-input 
                                    id="dashboard_background" 
                                    name="dashboard_background" 
                                    type="text" 
                                    class="mt-1 block w-full" 
                                    :value="old('dashboard_background', $settings['dashboard_background'] && strpos($settings['dashboard_background'], 'storage/') === false ? $settings['dashboard_background'] : '')"
                                    placeholder="e.g., #f5f5f5 or https://example.com/bg.jpg"
                                />
                                <p class="mt-2 text-xs text-gray-500">Examples: <code class="bg-gray-100 px-1 rounded">#f0f0f0</code>, <code class="bg-gray-100 px-1 rounded">rgb(240,240,240)</code>, <code class="bg-gray-100 px-1 rounded">https://example.com/bg.jpg</code></p>
                                @error('dashboard_background')
                                    <x-input-error class="mt-2" :messages="[$message]" />
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3">
                    <a href="{{ route('admin') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </a>
                    <x-primary-button>
                        {{ __('Save Settings') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

