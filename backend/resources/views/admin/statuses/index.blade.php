<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Groups') }}
            </h2>
            <button type="button" onclick="openStatusModal()" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                + New Group
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        These are the columns of the dashboard. A group can only be deleted once it has no projects;
                        move them to another group first.
                    </p>

                    <div class="space-y-2">
                        @foreach($statuses as $status)
                            @php($count = $counts[$status->key] ?? 0)
                            <div class="flex items-center gap-3 border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2">
                                <span class="w-4 h-4 rounded {{ $status->headerClass() }}"></span>
                                <span class="font-medium text-gray-800 dark:text-gray-200">{{ $status->label }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $count }} {{ Str::plural('project', $count) }}</span>
                                <div class="ml-auto flex items-center gap-3 text-sm">
                                    <button type="button" class="text-indigo-600 hover:underline"
                                        onclick='openStatusModal(@json(["id" => $status->id, "label" => $status->label, "color" => $status->safeColor()]))'>Edit</button>
                                    <form method="POST" action="{{ route('admin.statuses.destroy', $status) }}"
                                        onsubmit="return confirm('Delete the group &quot;{{ addslashes($status->label) }}&quot;?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline disabled:opacity-40 disabled:no-underline" @disabled($count > 0)
                                            @if($count > 0) title="Move its projects first" @endif>Delete</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="statusModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 overflow-y-auto">
        <div class="min-h-screen flex items-start justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mt-12">
                <form id="statusForm" method="POST" action="{{ route('admin.statuses.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="statusMethod" value="POST">
                    <input type="hidden" name="color" id="statusColor" value="{{ App\Support\NavColors::DEFAULT }}">

                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 id="statusModalTitle" class="text-lg font-semibold text-gray-800 dark:text-gray-200">New Group</h3>
                    </div>

                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                            <input type="text" name="label" id="statusLabel" required maxlength="100"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Colour</label>
                            <div class="mt-2 grid grid-cols-11 gap-1.5">
                                @foreach($colors as $color)
                                    <button type="button" data-color="{{ $color }}" onclick="pickStatusColor('{{ $color }}')"
                                        title="{{ ucfirst($color) }}"
                                        class="status-swatch h-7 rounded bg-{{ $color }}-600 ring-offset-1 dark:ring-offset-gray-800 transition"></button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-2">
                        <button type="button" onclick="closeStatusModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 dark:text-gray-200 rounded hover:bg-gray-300">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const statusUpdateUrl = @json(route('admin.statuses.update', ['status' => '__ID__']));
        const statusStoreUrl = @json(route('admin.statuses.store'));
        const defaultStatusColor = @json(App\Support\NavColors::DEFAULT);

        function pickStatusColor(color) {
            document.getElementById('statusColor').value = color;
            document.querySelectorAll('.status-swatch').forEach(btn => {
                const on = btn.dataset.color === color;
                btn.classList.toggle('ring-2', on);
                btn.classList.toggle('ring-gray-900', on);
                btn.classList.toggle('dark:ring-white', on);
                btn.classList.toggle('opacity-40', !on);
            });
        }

        function openStatusModal(data) {
            const form = document.getElementById('statusForm');
            const isEdit = !!data;
            form.action = isEdit ? statusUpdateUrl.replace('__ID__', data.id) : statusStoreUrl;
            document.getElementById('statusMethod').value = isEdit ? 'PUT' : 'POST';
            document.getElementById('statusModalTitle').textContent = isEdit ? 'Edit Group' : 'New Group';
            document.getElementById('statusLabel').value = isEdit ? data.label : '';
            pickStatusColor(isEdit ? data.color : defaultStatusColor);
            document.getElementById('statusModal').classList.remove('hidden');
            document.getElementById('statusLabel').focus();
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
        }
    </script>
    @endpush
</x-app-layout>
