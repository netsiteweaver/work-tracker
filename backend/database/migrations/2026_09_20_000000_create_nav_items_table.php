<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The top menu items used to live in config/navigation.php. They move to
     * the database here so they can be managed from the back office; the
     * config file keeps the admin-side items, which are tied to routes.
     *
     * The defaults a fresh install starts with are in Database\Seeders\NavItemSeeder.
     */
    public function up(): void
    {
        Schema::create('nav_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('nav_items')->cascadeOnDelete();
            $table->string('key')->unique();
            $table->string('label');
            $table->string('url')->nullable();
            $table->string('image')->nullable();
            $table->string('classes')->nullable();
            $table->string('image_classes')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nav_items');
    }
};
