<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('User Management') }}
            </h2>
            <button 
                onclick="document.getElementById('createUserModal').classList.remove('hidden')"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
            >
                + New User
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($users as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-wrap gap-1">
                                                @forelse($user->roles as $role)
                                                    <span class="px-2 py-1 text-xs rounded-full 
                                                        @if($role->slug === 'admin') bg-red-100 text-red-800
                                                        @elseif($role->slug === 'editor') bg-blue-100 text-blue-800
                                                        @else bg-gray-100 text-gray-800
                                                        @endif">
                                                        {{ $role->name }}
                                                    </span>
                                                @empty
                                                    <span class="text-xs text-gray-400">No roles</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button 
                                                onclick="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', [{{ $user->roles->pluck('id')->implode(',') }}])"
                                                class="text-indigo-600 hover:text-indigo-900 mr-3"
                                            >
                                                Edit
                                            </button>
                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create User Modal -->
    <div id="createUserModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Create New User</h3>
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="roles" :value="__('Roles')" />
                            <div class="mt-2 space-y-2">
                                @foreach($roles as $role)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ $role->name }}</span>
                                        <span class="ml-2 text-xs text-gray-500">({{ $role->description }})</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 pt-4">
                            <button 
                                type="button"
                                onclick="document.getElementById('createUserModal').classList.add('hidden')"
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <x-primary-button>Create User</x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Edit User</h3>
                <form id="editUserForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="edit_name" :value="__('Name')" />
                            <x-text-input id="edit_name" name="name" type="text" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="edit_email" :value="__('Email')" />
                            <x-text-input id="edit_email" name="email" type="email" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="edit_password" :value="__('Password (leave blank to keep current)')" />
                            <x-text-input id="edit_password" name="password" type="password" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label for="edit_password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input id="edit_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label for="edit_roles" :value="__('Roles')" />
                            <div class="mt-2 space-y-2" id="edit_roles_container">
                                @foreach($roles as $role)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="edit-role-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ $role->name }}</span>
                                        <span class="ml-2 text-xs text-gray-500">({{ $role->description }})</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 pt-4">
                            <button 
                                type="button"
                                onclick="document.getElementById('editUserModal').classList.add('hidden')"
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <x-primary-button>Update User</x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(userId, name, email, roleIds) {
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('editUserForm').action = '/admin/users/' + userId;
            
            // Clear all checkboxes
            document.querySelectorAll('.edit-role-checkbox').forEach(cb => cb.checked = false);
            
            // Check the user's roles
            roleIds.forEach(roleId => {
                const checkbox = document.querySelector(`.edit-role-checkbox[value="${roleId}"]`);
                if (checkbox) checkbox.checked = true;
            });
            
            document.getElementById('editUserModal').classList.remove('hidden');
        }
    </script>
</x-app-layout>

