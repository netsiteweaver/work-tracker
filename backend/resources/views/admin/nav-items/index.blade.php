<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Top Menu') }}
            </h2>
            <button
                type="button"
                onclick="openNavModal()"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
            >
                + New Item
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
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
                        These are the buttons on the top menu of the front end. Drag to change their
                        default order &mdash; each user can still pin and re-sort their own copy of the menu.
                    </p>

                    <div id="nav-list" class="space-y-3">
                        @forelse($items as $item)
                            <div class="nav-row border border-gray-200 dark:border-gray-700 rounded-lg" data-id="{{ $item->id }}">
                                @include('admin.nav-items.partials.row', ['item' => $item, 'isChild' => false])

                                <div class="nav-children pl-10 pr-3 pb-3 space-y-2" data-parent="{{ $item->id }}">
                                    @foreach($item->children as $child)
                                        <div class="nav-row border border-gray-100 dark:border-gray-700 rounded" data-id="{{ $child->id }}">
                                            @include('admin.nav-items.partials.row', ['item' => $child, 'isChild' => true])
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No menu items yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create / edit modal -->
    <div id="navModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 overflow-y-auto">
        <div class="min-h-screen flex items-start justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg mt-12">
                <form id="navForm" method="POST" action="{{ route('admin.nav-items.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="navMethod" value="POST">

                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 id="navModalTitle" class="text-lg font-semibold text-gray-800 dark:text-gray-200">New Menu Item</h3>
                    </div>

                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Label</label>
                            <input type="text" name="label" id="navLabel" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Link</label>
                            <input type="url" name="url" id="navUrl" placeholder="https://example.com"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200 shadow-sm">
                            <p id="navUrlHint" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Opens in a new tab. Leave empty only for an item that just opens a dropdown.
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Inside</label>
                            <select name="parent_id" id="navParent"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200 shadow-sm">
                                <option value="">Top level</option>
                                @foreach($items as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->label }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Pick a parent to make this a dropdown entry under it.
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Icon</label>
                            <div class="mt-1 flex items-center gap-3">
                                <img id="navImagePreview" src="" alt="" class="w-8 h-8 hidden rounded bg-gray-100 dark:bg-gray-900 object-contain">
                                <input type="file" name="image" accept="image/*"
                                    class="block w-full text-sm text-gray-600 dark:text-gray-400">
                            </div>
                            <label id="navRemoveImageWrap" class="mt-2 hidden items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300">
                                Remove current icon
                            </label>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Colour</label>
                            <select id="navPreset" onchange="applyPreset(this.value)"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200 shadow-sm">
                                <option value="">Custom / none</option>
                                @foreach($presets as $name => $classes)
                                    <option value="{{ $classes }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="classes" id="navClasses" placeholder="Tailwind classes"
                                class="mt-2 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200 shadow-sm font-mono text-xs">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Icon classes</label>
                            <input type="text" name="image_classes" id="navImageClasses" placeholder="e.g. bg-white rounded-sm p-0.5"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200 shadow-sm font-mono text-xs">
                        </div>

                        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" name="is_active" id="navActive" value="1" checked class="rounded border-gray-300">
                            Show in the menu
                        </label>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                        <button type="button" onclick="closeNavModal()"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 dark:text-gray-200 rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        const NAV_STORE_URL = @json(route('admin.nav-items.store'));
        const NAV_ORDER_URL = @json(route('admin.nav-items.order'));
        const NAV_UPDATE_URL = @json(route('admin.nav-items.update', ['navItem' => '__ID__']));

        function openNavModal(data) {
            const form = document.getElementById('navForm');
            const isEdit = !!data;

            form.action = isEdit ? NAV_UPDATE_URL.replace('__ID__', data.id) : NAV_STORE_URL;
            document.getElementById('navMethod').value = isEdit ? 'PUT' : 'POST';
            document.getElementById('navModalTitle').textContent = isEdit ? 'Edit Menu Item' : 'New Menu Item';

            form.reset();
            document.getElementById('navLabel').value = isEdit ? data.label : '';
            document.getElementById('navUrl').value = isEdit && data.url ? data.url : '';
            document.getElementById('navParent').value = isEdit && data.parent_id ? data.parent_id : '';
            document.getElementById('navClasses').value = isEdit && data.classes ? data.classes : '';
            document.getElementById('navImageClasses').value = isEdit && data.image_classes ? data.image_classes : '';
            document.getElementById('navActive').checked = isEdit ? !!data.is_active : true;
            document.getElementById('navPreset').value = '';

            // An item with its own dropdown entries can neither be nested nor need a link.
            const hasChildren = isEdit && data.has_children;
            const parent = document.getElementById('navParent');
            parent.disabled = hasChildren;
            document.getElementById('navUrl').required = !hasChildren;
            document.getElementById('navUrlHint').textContent = hasChildren
                ? 'This item opens its dropdown entries, so it needs no link of its own.'
                : 'Opens in a new tab. Leave empty only for an item that just opens a dropdown.';

            // Its own row would be a nonsense parent.
            Array.from(parent.options).forEach(function (option) {
                option.hidden = isEdit && option.value === String(data.id);
            });

            const preview = document.getElementById('navImagePreview');
            const removeWrap = document.getElementById('navRemoveImageWrap');
            if (isEdit && data.image_url) {
                preview.src = data.image_url;
                preview.classList.remove('hidden');
                removeWrap.classList.remove('hidden');
                removeWrap.classList.add('flex');
            } else {
                preview.classList.add('hidden');
                removeWrap.classList.add('hidden');
                removeWrap.classList.remove('flex');
            }

            document.getElementById('navModal').classList.remove('hidden');
        }

        function closeNavModal() {
            document.getElementById('navModal').classList.add('hidden');
        }

        function applyPreset(value) {
            if (value) {
                document.getElementById('navClasses').value = value;
            }
        }

        document.getElementById('navModal').addEventListener('click', function (event) {
            if (event.target === this) {
                closeNavModal();
            }
        });

        (function () {
            function saveOrder(container) {
                const ids = Array.from(container.children)
                    .filter(function (el) { return el.classList.contains('nav-row'); })
                    .map(function (el) { return el.dataset.id; });

                fetch(NAV_ORDER_URL, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ ids: ids }),
                }).catch(function () {});
            }

            const options = {
                animation: 150,
                draggable: '.nav-row',
                handle: '.nav-handle',
                onEnd: function (event) { saveOrder(event.to); },
            };

            const list = document.getElementById('nav-list');
            if (list) {
                new Sortable(list, options);
                // Dropdown entries reorder within their own parent only.
                document.querySelectorAll('.nav-children').forEach(function (group) {
                    new Sortable(group, options);
                });
            }
        })();
    </script>
</x-app-layout>
