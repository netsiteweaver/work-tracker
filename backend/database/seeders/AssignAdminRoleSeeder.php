<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssignAdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();
        
        if ($adminRole) {
            // Assign admin role to the first user (or all existing users if you prefer)
            $firstUser = User::first();
            if ($firstUser && !$firstUser->hasRole('admin')) {
                $firstUser->roles()->attach($adminRole->id);
                $this->command->info('Admin role assigned to first user.');
            }
        }
    }
}
