<?php

use App\Support\NavColors;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A button is now described by a colour plus an optional outline style,
     * rather than a hand-written class string. The `classes` column stays as
     * an escape hatch for anything the palette cannot express.
     */
    public function up(): void
    {
        Schema::table('nav_items', function (Blueprint $table) {
            $table->string('color')->nullable()->after('image');
            $table->boolean('outline')->default(false)->after('color');
        });

        // Read the existing class strings back into colour + outline, so the
        // menu keeps its look and stops depending on un-scanned class names.
        foreach (DB::table('nav_items')->whereNotNull('classes')->get() as $row) {
            if ($parsed = NavColors::parse($row->classes)) {
                DB::table('nav_items')->where('id', $row->id)->update([
                    'color' => $parsed[0],
                    'outline' => $parsed[1],
                    'classes' => null,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('nav_items', function (Blueprint $table) {
            $table->dropColumn(['color', 'outline']);
        });
    }
};
