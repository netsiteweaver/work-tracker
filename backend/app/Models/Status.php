<?php

namespace App\Models;

use App\Support\NavColors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * A dashboard group: one Kanban column, and the value of projects.status.
 *
 * The colour class names are built from the palette in NavColors, which
 * tailwind.config.js safelists.
 */
class Status extends Model
{
    protected $fillable = ['key', 'label', 'color', 'sort_order'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    /**
     * @return Collection<int, Status> keyed by status key, in default order
     */
    public static function ordered(): Collection
    {
        return self::orderBy('sort_order')->orderBy('id')->get()->keyBy('key');
    }

    /**
     * A unique key for a new group, derived from its label.
     */
    public static function makeKey(string $label): string
    {
        $base = str_replace('-', '_', Str::slug($label)) ?: 'group';
        $key = $base;
        for ($i = 2; self::where('key', $key)->exists(); $i++) {
            $key = "{$base}_{$i}";
        }

        return $key;
    }

    /**
     * Value that changes when a group is added, removed or edited.
     */
    public static function syncFingerprint(): string
    {
        $row = DB::table('statuses')
            ->selectRaw('COUNT(*) as c')
            ->selectRaw('MAX(updated_at) as max_updated')
            ->first();

        return ($row->c ?? 0).'|'.($row->max_updated ?? '');
    }

    public function safeColor(): string
    {
        return in_array($this->color, NavColors::COLORS, true) ? $this->color : NavColors::DEFAULT;
    }

    public function headerClass(): string
    {
        return "bg-{$this->safeColor()}-600";
    }

    public function borderClass(): string
    {
        return "border-{$this->safeColor()}-500";
    }

    public function columnClass(): string
    {
        return "bg-{$this->safeColor()}-50";
    }
}
