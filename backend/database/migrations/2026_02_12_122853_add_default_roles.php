<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Full access to all features',
                'permissions' => json_encode([
                    'view_projects',
                    'edit_projects',
                    'delete_projects',
                    'create_projects',
                    'manage_users',
                    'manage_settings',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Editor',
                'slug' => 'editor',
                'description' => 'Can view and edit projects',
                'permissions' => json_encode([
                    'view_projects',
                    'edit_projects',
                    'create_projects',
                    'manage_settings',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Viewer',
                'slug' => 'viewer',
                'description' => 'Can only view projects, no editing',
                'permissions' => json_encode([
                    'view_projects',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('roles')->insert($roles);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('roles')->whereIn('slug', ['admin', 'editor', 'viewer'])->delete();
    }
};
