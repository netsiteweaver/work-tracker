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
                    <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

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
                            <a href="{{ route('admin') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
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
</x-app-layout>

