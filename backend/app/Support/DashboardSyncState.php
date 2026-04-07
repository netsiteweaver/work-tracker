<?php

namespace App\Support;

use App\Models\Project;
use App\Models\Setting;

class DashboardSyncState
{
    /**
     * Canonical left-to-right Kanban column order.
     *
     * @return list<string>
     */
    public static function defaultColumnOrder(): array
    {
        return ['new', 'in_progress', 'on_hold', 'maintenance', 'completed', 'stopped'];
    }

    /**
     * @return list<string>
     */
    public static function normalizedColumnOrder(?int $userId): array
    {
        $default = self::defaultColumnOrder();
        if (! $userId) {
            return $default;
        }

        $order = Setting::get('column_order', $default, $userId);
        if (! is_array($order)) {
            return $default;
        }

        $order = array_values(array_filter($order, fn (mixed $s) => is_string($s) && in_array($s, $default, true)));
        $missing = array_values(array_diff($default, $order));

        return array_merge($order, $missing);
    }

    /**
     * Fingerprint for dashboard polling: projects plus per-user Kanban column order.
     */
    public static function fingerprint(?int $userId): string
    {
        $base = Project::syncFingerprint();
        if (! $userId) {
            return $base;
        }

        $order = self::normalizedColumnOrder($userId);

        return $base.'|co:'.md5(json_encode($order));
    }
}
