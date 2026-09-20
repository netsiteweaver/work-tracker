<?php

namespace App\Support;

use App\Models\NavItem;
use App\Models\Setting;

class NavMenu
{
    /** The group whose items are managed from the back office. */
    public const MANAGED_GROUP = 'frontend';

    /** Per-user setting keys. */
    public const PINS_KEY = 'nav_pins';
    public const USAGE_KEY = 'nav_usage';

    /**
     * Items of a menu group, filtered by what the current user may see and
     * ordered by the user's pins and click counts.
     *
     * @return list<array<string, mixed>>
     */
    public static function items(string $group, ?int $userId = null): array
    {
        $userId = $userId ?? auth()->id();
        $items = self::visibleItems($group);

        return self::ordered($items, self::pins($userId), self::usage($userId));
    }

    /**
     * Every key of a group, whether or not the current user may see it.
     *
     * @return list<string>
     */
    public static function keys(string $group): array
    {
        return array_column(self::groupItems($group), 'key');
    }

    /**
     * @return list<string>
     */
    public static function allKeys(): array
    {
        $keys = [];
        foreach (self::groups() as $group) {
            foreach (self::groupItems($group) as $item) {
                $keys[] = $item['key'];
                foreach ($item['children'] ?? [] as $child) {
                    $keys[] = $child['key'];
                }
            }
        }

        return $keys;
    }

    /**
     * @return array<string, int> key => pinned slot
     */
    public static function pins(?int $userId = null): array
    {
        return self::intMap(Setting::get(self::PINS_KEY, [], $userId ?? auth()->id()));
    }

    /**
     * @return array<string, int> key => click count
     */
    public static function usage(?int $userId = null): array
    {
        return self::intMap(Setting::get(self::USAGE_KEY, [], $userId ?? auth()->id()));
    }

    /**
     * Order items so that pinned ones hold their slot and the rest fill the
     * gaps most-clicked first. Mirrored in resources/views/layouts/navigation.blade.php
     * for guests, who keep their preferences in localStorage.
     *
     * @param  list<array<string, mixed>>  $items
     * @param  array<string, int>  $pins
     * @param  array<string, int>  $usage
     * @return list<array<string, mixed>>
     */
    public static function ordered(array $items, array $pins, array $usage): array
    {
        $items = array_values($items);
        $total = count($items);
        if ($total === 0) {
            return [];
        }

        $byKey = [];
        foreach ($items as $index => $item) {
            $byKey[$item['key']] = $index;
        }

        // Pinned keys claim their slot; the first one wins a contested slot.
        $pins = array_intersect_key($pins, $byKey);
        asort($pins);
        $pinnedBySlot = [];
        foreach ($pins as $key => $slot) {
            $slot = max(0, min($slot, $total - 1));
            while (isset($pinnedBySlot[$slot]) && $slot < $total - 1) {
                $slot++;
            }
            while (isset($pinnedBySlot[$slot]) && $slot > 0) {
                $slot--;
            }
            $pinnedBySlot[$slot] = $key;
        }

        // Everything else: most clicked first, default order breaking ties.
        $queue = array_values(array_diff(array_keys($byKey), $pinnedBySlot));
        usort($queue, function (string $a, string $b) use ($usage, $byKey) {
            return [$usage[$b] ?? 0, $byKey[$a]] <=> [$usage[$a] ?? 0, $byKey[$b]];
        });

        $ordered = [];
        for ($slot = 0; $slot < $total; $slot++) {
            $key = $pinnedBySlot[$slot] ?? array_shift($queue);
            if ($key === null) {
                continue;
            }
            $item = $items[$byKey[$key]];
            $item['pinned'] = isset($pinnedBySlot[$slot]);
            $ordered[] = $item;
        }

        return $ordered;
    }

    /**
     * Every menu group there is.
     *
     * @return list<string>
     */
    public static function groups(): array
    {
        return array_values(array_unique(array_merge(
            [self::MANAGED_GROUP],
            array_keys(config('navigation', []))
        )));
    }

    /**
     * All items of a group in their default order, before any filtering. The
     * managed group is editable from the back office and so lives in the
     * database; the rest are tied to routes and stay in config/navigation.php.
     *
     * @return list<array<string, mixed>>
     */
    public static function groupItems(string $group): array
    {
        if ($group !== self::MANAGED_GROUP) {
            return config('navigation.'.$group, []);
        }

        return NavItem::tree(activeOnly: true)
            ->map(fn (NavItem $item) => $item->toMenuArray())
            ->all();
    }

    /**
     * Items of a group the current user is allowed to see.
     *
     * @return list<array<string, mixed>>
     */
    protected static function visibleItems(string $group): array
    {
        return array_values(array_filter(
            self::groupItems($group),
            fn (array $item) => self::allowed($item['ability'] ?? null)
        ));
    }

    protected static function allowed(?string $ability): bool
    {
        if ($ability === null) {
            return true;
        }

        $user = auth()->user();
        if (! $user) {
            return false;
        }

        return match ($ability) {
            'edit' => $user->canEdit(),
            'admin' => $user->isAdmin(),
            default => false,
        };
    }

    /**
     * @return array<string, int>
     */
    protected static function intMap(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $map = [];
        foreach ($value as $key => $number) {
            if (is_string($key) && is_numeric($number)) {
                $map[$key] = (int) $number;
            }
        }

        return $map;
    }
}
