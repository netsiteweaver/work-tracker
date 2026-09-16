{{--
    Top menu ordering.

    Dragging an item pins it to the slot it was dropped in; every item left
    unpinned flows around the pins, most-clicked first. Signed-in users keep
    their pins and click counts on the server (App\Support\NavMenu, which does
    the same ordering server-side so the menu never reshuffles after paint);
    guests keep theirs in localStorage, which is why applyOrder() below mirrors
    NavMenu::ordered().
--}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    (function () {
        const container = document.getElementById('nav-sortable');
        if (!container) {
            return;
        }

        const AUTH = @json(auth()->check());
        const PIN_URL = @json(route('nav.pin'));
        const USAGE_URL = @json(route('nav.usage'));
        const PINS_STORAGE = 'navPins';
        const USAGE_STORAGE = 'navUsage';

        function readStore(name) {
            try {
                const raw = localStorage.getItem(name);
                const parsed = raw ? JSON.parse(raw) : null;
                return parsed && typeof parsed === 'object' ? parsed : {};
            } catch (e) {
                return {};
            }
        }

        function writeStore(name, value) {
            try {
                localStorage.setItem(name, JSON.stringify(value));
            } catch (e) {
                // Private browsing / full storage: server state still wins for signed-in users.
            }
        }

        function post(url, body) {
            fetch(url, {
                method: 'POST',
                keepalive: true,
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(body),
            }).catch(function () {});
        }

        function items() {
            return Array.from(container.children).filter(function (el) {
                return el.classList.contains('nav-item');
            });
        }

        function markPinned(el, pinned) {
            el.dataset.navPinned = pinned ? 'true' : 'false';
            const button = el.querySelector('.nav-pin');
            if (button) {
                button.title = pinned ? 'Unpin — sort by usage again' : 'Pin to this position';
            }
        }

        /** Pinned slots as they currently sit in the DOM. */
        function currentPins() {
            const pins = {};
            items().forEach(function (el, index) {
                if (el.dataset.navPinned === 'true') {
                    pins[el.dataset.navKey] = index;
                }
            });
            return pins;
        }

        function savePins() {
            const pins = currentPins();
            writeStore(PINS_STORAGE, pins);
            if (AUTH) {
                post(PIN_URL, { pins: pins });
            }
        }

        function trackClick(key) {
            const usage = readStore(USAGE_STORAGE);
            usage[key] = (usage[key] || 0) + 1;
            writeStore(USAGE_STORAGE, usage);
            if (AUTH) {
                post(USAGE_URL, { key: key });
            }
        }

        /** Mirror of App\Support\NavMenu::ordered(), for guests. */
        function applyOrder(pins, usage) {
            const elements = items();
            const total = elements.length;
            if (!total) {
                return;
            }

            const indexOfKey = {};
            elements.forEach(function (el, index) {
                indexOfKey[el.dataset.navKey] = index;
            });

            const bySlot = {};
            Object.keys(pins)
                .filter(function (key) { return key in indexOfKey; })
                .map(function (key) {
                    const slot = parseInt(pins[key], 10);
                    return [key, Math.max(0, Math.min(isNaN(slot) ? 0 : slot, total - 1))];
                })
                .sort(function (a, b) { return a[1] - b[1]; })
                .forEach(function (entry) {
                    let slot = entry[1];
                    while (bySlot[slot] !== undefined && slot < total - 1) {
                        slot++;
                    }
                    while (bySlot[slot] !== undefined) {
                        slot--;
                    }
                    bySlot[slot] = entry[0];
                });

            const pinnedKeys = Object.keys(bySlot).map(function (slot) { return bySlot[slot]; });
            const queue = Object.keys(indexOfKey)
                .filter(function (key) { return pinnedKeys.indexOf(key) === -1; })
                .sort(function (a, b) {
                    return (usage[b] || 0) - (usage[a] || 0) || indexOfKey[a] - indexOfKey[b];
                });

            for (let slot = 0; slot < total; slot++) {
                const isPinned = bySlot[slot] !== undefined;
                const key = isPinned ? bySlot[slot] : queue.shift();
                if (key === undefined) {
                    continue;
                }
                const el = elements[indexOfKey[key]];
                markPinned(el, isPinned);
                container.appendChild(el);
            }
        }

        // Signed-in users get the order rendered server-side already.
        if (!AUTH) {
            applyOrder(readStore(PINS_STORAGE), readStore(USAGE_STORAGE));
        } else {
            items().forEach(function (el) {
                markPinned(el, el.dataset.navPinned === 'true');
            });
        }

        // Swallow the click the browser fires on the item we just dropped.
        let dragging = false;

        container.addEventListener('click', function (event) {
            if (dragging) {
                event.preventDefault();
                event.stopPropagation();
            }
        }, true);

        container.addEventListener('click', function (event) {
            const pinButton = event.target.closest('.nav-pin');
            if (pinButton) {
                event.preventDefault();
                event.stopPropagation();
                const item = pinButton.closest('.nav-item');
                markPinned(item, item.dataset.navPinned !== 'true');
                savePins();
                return;
            }

            const item = event.target.closest('.nav-item');
            if (!item || !container.contains(item)) {
                return;
            }

            // For a dropdown, opening the menu is not a use of it — following a link is.
            if (item.dataset.navChildren === 'true' && !event.target.closest('a[href]')) {
                return;
            }

            trackClick(item.dataset.navKey);
        });

        new Sortable(container, {
            animation: 200,
            draggable: '.nav-item',
            filter: '.nav-pin',
            preventOnFilter: false,
            forceFallback: true,
            fallbackTolerance: 4,
            delay: 180,
            delayOnTouchOnly: false,
            ghostClass: 'nav-ghost',
            chosenClass: 'nav-chosen',
            onStart: function () {
                dragging = true;
            },
            onEnd: function (event) {
                // A drag is a deliberate placement, so the item it moved stays put.
                markPinned(event.item, true);
                savePins();
                setTimeout(function () { dragging = false; }, 0);
            },
        });
    })();
</script>
