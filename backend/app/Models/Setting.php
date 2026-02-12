<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    protected $fillable = [
        'user_id',
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get a setting value by key for the current user
     */
    public static function get($key, $default = null, $userId = null)
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) {
            return $default;
        }
        
        $setting = self::where('user_id', $userId)
            ->where('key', $key)
            ->first();
        
        if (!$setting) {
            return $default;
        }
        
        // Try to decode JSON, if it fails return as string
        $decoded = json_decode($setting->value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $setting->value;
    }

    /**
     * Set a setting value by key for the current user
     */
    public static function set($key, $value, $userId = null)
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) {
            return false;
        }
        
        $value = is_array($value) ? json_encode($value) : $value;
        
        return self::updateOrCreate(
            ['user_id' => $userId, 'key' => $key],
            ['value' => $value]
        );
    }
}
