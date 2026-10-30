<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The dashboard groups (Kanban columns) move from a fixed enum to a table,
     * so they can be added and removed from the back office. The six that
     * existed before are carried over with their old colours.
     */
    public function up(): void
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->string('color')->default('blue');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $defaults = [
            ['new', 'New', 'blue'],
            ['in_progress', 'In Progress', 'yellow'],
            ['on_hold', 'On Hold', 'orange'],
            ['maintenance', 'Maintenance', 'purple'],
            ['completed', 'Completed', 'green'],
            ['stopped', 'Stopped', 'red'],
        ];

        foreach ($defaults as $position => [$key, $label, $color]) {
            DB::table('statuses')->insert([
                'key' => $key,
                'label' => $label,
                'color' => $color,
                'sort_order' => $position,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE projects MODIFY COLUMN status VARCHAR(100) NOT NULL DEFAULT 'new'");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};
