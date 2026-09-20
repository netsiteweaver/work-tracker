<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Support\NavColors;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * A button on the top menu. An item with children renders as a dropdown;
 * everything else is a plain link. See App\Support\NavMenu for the ordering.
 */
class NavItem extends Model
{
    protected $fillable = [
        'parent_id',
        'key',
        'label',
        'url',
        'image',
        'color',
        'outline',
        'classes',
        'image_classes',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'outline' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order')->orderBy('id');
    }

    /**
     * The classes the button renders with: generated from its colour, or the
     * raw string for an item styled by hand.
     */
    public function getButtonClassesAttribute(): string
    {
        return $this->color
            ? NavColors::classes($this->color, (bool) $this->outline)
            : (string) $this->classes;
    }

    /**
     * Public URL of the item's icon. Images uploaded from the back office live
     * on the public disk; the ones shipped with the app sit in public/images.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return Str::startsWith($this->image, ['http://', 'https://', '/'])
            ? $this->image
            : asset(Storage::disk('public')->exists($this->image) ? 'storage/'.$this->image : $this->image);
    }

    /**
     * True when the image is a file we uploaded (and so may delete).
     */
    public function hasUploadedImage(): bool
    {
        return $this->image
            && ! Str::startsWith($this->image, ['http://', 'https://', '/'])
            && Storage::disk('public')->exists($this->image);
    }

    /**
     * Top-level items, in their default order, with children attached.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, self>
     */
    public static function tree(bool $activeOnly = false): \Illuminate\Database\Eloquent\Collection
    {
        $query = self::query()->whereNull('parent_id');

        if ($activeOnly) {
            $query->where('is_active', true)
                ->with(['children' => fn ($q) => $q->where('is_active', true)]);
        } else {
            $query->with('children');
        }

        return $query->orderBy('sort_order')->orderBy('id')->get();
    }

    /**
     * Shape expected by the menu blade / App\Support\NavMenu.
     *
     * @return array<string, mixed>
     */
    public function toMenuArray(): array
    {
        $item = [
            'key' => $this->key,
            'label' => $this->label,
            'url' => $this->url,
            'image' => $this->image,
            'image_url' => $this->image_url,
            'classes' => $this->button_classes,
            'image_classes' => $this->image_classes,
        ];

        $children = $this->children->map(fn (self $child) => $child->toMenuArray())->all();
        if ($children) {
            $item['children'] = $children;
        }

        return $item;
    }

    /**
     * A unique, stable key derived from a label.
     */
    public static function makeKey(string $label, ?int $ignoreId = null): string
    {
        $base = Str::slug($label) ?: 'item';
        $key = $base;
        $suffix = 2;

        while (self::where('key', $key)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $key = $base.'-'.$suffix++;
        }

        return $key;
    }
}
