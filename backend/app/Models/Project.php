<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'maintenance',
        'dev_path',
        'staging_url',
        'production_url',
        'status',
        'start_date',
        'finish_date',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'finish_date' => 'date',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Value that changes when any project row changes (for cross-tab / cross-browser sync).
     */
    public static function syncFingerprint(): string
    {
        $row = DB::table('projects')
            ->selectRaw('COUNT(*) as c')
            ->selectRaw('COALESCE(MAX(id), 0) as max_id')
            ->selectRaw('MAX(updated_at) as max_updated')
            ->first();

        return ($row->c ?? 0).'|'.($row->max_id ?? 0).'|'.($row->max_updated ?? '');
    }
}
