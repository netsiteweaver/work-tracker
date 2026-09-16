<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Project') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.projects.update', $project) }}" method="POST" class="space-y-4" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="return_to" value="{{ old('return_to', $returnTo) }}">

                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $project->name)" required autofocus />
                            @error('name')
                                <x-input-error class="mt-2" :messages="[$message]" />
                            @enderror
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $project->description) }}</textarea>
                            @error('description')
                                <x-input-error class="mt-2" :messages="[$message]" />
                            @enderror
                        </div>

                        <div>
                            <x-input-label for="maintenance" :value="__('Maintenance')" />
                            <textarea id="maintenance" name="maintenance" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('maintenance', $project->maintenance) }}</textarea>
                            @error('maintenance')
                                <x-input-error class="mt-2" :messages="[$message]" />
                            @enderror
                        </div>

                        <div>
                            <x-input-label for="image" :value="__('Image')" />
                            @if($project->image)
                                <div class="mt-1 mb-2 flex items-center gap-4">
                                    <img src="{{ $project->image_url }}" alt="Current project image" class="max-w-xs max-h-32 rounded border border-gray-300" />
                                    <label for="remove_image" class="flex items-center text-sm text-gray-700">
                                        <input type="checkbox" id="remove_image" name="remove_image" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                                        <span class="ml-2">Remove image</span>
                                    </label>
                                </div>
                            @endif
                            <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" onchange="previewImage(this)" />
                            <p class="mt-2 text-xs text-gray-500">Optional. Shown as the background of the project card (JPG, PNG, GIF, WebP - Max 50MB){{ $project->image ? '. Uploading a new image replaces the current one.' : '' }}</p>
                            <div id="image-preview-container" class="mt-4 hidden">
                                <p class="text-sm font-medium text-gray-700 mb-2">{{ $project->image ? 'New image preview:' : 'Preview:' }}</p>
                                <div class="relative inline-block">
                                    <img id="image-preview" alt="Image preview" class="max-w-xs max-h-48 rounded border border-gray-300 shadow-sm" />
                                    <button type="button" onclick="clearImagePreview()" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors" title="Remove selected image">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            @error('image')
                                <x-input-error class="mt-2" :messages="[$message]" />
                            @enderror
                        </div>

                        <div>
                            <x-input-label for="image_position" :value="__('Image Display')" />
                            <select id="image_position" name="image_position" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="background" {{ old('image_position', $project->image_position) == 'background' ? 'selected' : '' }}>Background of the card</option>
                                <option value="left" {{ old('image_position', $project->image_position) == 'left' ? 'selected' : '' }}>Left of the text</option>
                                <option value="right" {{ old('image_position', $project->image_position) == 'right' ? 'selected' : '' }}>Right of the text</option>
                            </select>
                            <p class="mt-2 text-xs text-gray-500">Where the image appears on the project card. Only used when an image is set.</p>
                            @error('image_position')
                                <x-input-error class="mt-2" :messages="[$message]" />
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="dev_path" :value="__('Dev Path')" />
                                <x-text-input id="dev_path" name="dev_path" type="text" class="mt-1 block w-full" :value="old('dev_path', $project->dev_path)" />
                                @error('dev_path')
                                    <x-input-error class="mt-2" :messages="[$message]" />
                                @enderror
                            </div>

                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="new" {{ old('status', $project->status) == 'new' ? 'selected' : '' }}>New</option>
                                    <option value="in_progress" {{ old('status', $project->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="on_hold" {{ old('status', $project->status) == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                    <option value="maintenance" {{ old('status', $project->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                    <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="stopped" {{ old('status', $project->status) == 'stopped' ? 'selected' : '' }}>Stopped</option>
                                </select>
                                @error('status')
                                    <x-input-error class="mt-2" :messages="[$message]" />
                                @enderror
                            </div>
                        </div>

                        <div>
                            <x-input-label for="staging_url" :value="__('Staging URL')" />
                            <x-text-input id="staging_url" name="staging_url" type="url" class="mt-1 block w-full" :value="old('staging_url', $project->staging_url)" />
                            @error('staging_url')
                                <x-input-error class="mt-2" :messages="[$message]" />
                            @enderror
                        </div>

                        <div>
                            <x-input-label for="production_url" :value="__('Production URL')" />
                            <x-text-input id="production_url" name="production_url" type="url" class="mt-1 block w-full" :value="old('production_url', $project->production_url)" />
                            @error('production_url')
                                <x-input-error class="mt-2" :messages="[$message]" />
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="start_date" :value="__('Start Date')" />
                                <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date', $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : '')" />
                                @error('start_date')
                                    <x-input-error class="mt-2" :messages="[$message]" />
                                @enderror
                            </div>

                            <div>
                                <x-input-label for="finish_date" :value="__('Finish Date')" />
                                <x-text-input id="finish_date" name="finish_date" type="date" class="mt-1 block w-full" :value="old('finish_date', $project->finish_date ? \Carbon\Carbon::parse($project->finish_date)->format('Y-m-d') : '')" />
                                @error('finish_date')
                                    <x-input-error class="mt-2" :messages="[$message]" />
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ $returnTo === 'dashboard' ? route('dashboard') : route('admin.projects') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Update Project') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function previewImage(input) {
            const file = input.files && input.files[0];
            if (!file) {
                clearImagePreview();
                return;
            }

            // Check file size (50MB = 50 * 1024 * 1024 bytes)
            if (file.size > 50 * 1024 * 1024) {
                alert('File size exceeds 50MB limit. Please choose a smaller file.');
                clearImagePreview();
                return;
            }

            const preview = document.getElementById('image-preview');
            if (preview.src.startsWith('blob:')) {
                URL.revokeObjectURL(preview.src);
            }
            preview.src = URL.createObjectURL(file);
            document.getElementById('image-preview-container').classList.remove('hidden');
        }

        function clearImagePreview() {
            const preview = document.getElementById('image-preview');
            if (preview.src.startsWith('blob:')) {
                URL.revokeObjectURL(preview.src);
            }
            preview.removeAttribute('src');
            document.getElementById('image-preview-container').classList.add('hidden');
            document.getElementById('image').value = '';
        }
    </script>
    @endpush
</x-app-layout>

