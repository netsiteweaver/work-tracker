<?php

/*
|--------------------------------------------------------------------------
| Top menu items
|--------------------------------------------------------------------------
|
| Items are listed here in their default order. Each user can override that
| order: dragging an item pins it to a fixed slot, and everything left
| unpinned flows around the pins sorted by how often it has been clicked.
| See App\Support\NavMenu.
|
| Only the admin-side menu lives here, as its items point at routes that live
| in code. The front-end menu is managed from the back office and stored in the
| nav_items table; its defaults are in Database\Seeders\NavItemSeeder.
|
*/

return [

    // Shown on the admin side of the app.
    'backend' => [
        [
            'key' => 'admin-projects',
            'label' => 'Projects',
            'route' => 'admin.projects',
            'active' => 'admin.projects*',
            'ability' => 'edit',
            'classes' => 'bg-blue-600 text-white hover:bg-blue-700',
            'active_classes' => 'ring-2 ring-blue-300',
            'icon' => ['M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
        ],
        [
            'key' => 'admin-settings',
            'label' => 'Settings',
            'route' => 'admin.settings',
            'active' => 'admin.settings*',
            'ability' => 'edit',
            'classes' => 'bg-indigo-600 text-white hover:bg-indigo-700',
            'active_classes' => 'ring-2 ring-indigo-300',
            'icon' => [
                'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
                'M15 12a3 3 0 11-6 0 3 3 0 016 0z',
            ],
        ],
        [
            'key' => 'admin-menu',
            'label' => 'Menu',
            'route' => 'admin.nav-items.index',
            'active' => 'admin.nav-items.*',
            'ability' => 'edit',
            'classes' => 'bg-cyan-600 text-white hover:bg-cyan-700',
            'active_classes' => 'ring-2 ring-cyan-300',
            'icon' => ['M4 6h16M4 12h16M4 18h16'],
        ],
        [
            'key' => 'admin-users',
            'label' => 'Users',
            'route' => 'admin.users.index',
            'active' => 'admin.users*',
            'ability' => 'admin',
            'classes' => 'bg-green-600 text-white hover:bg-green-700',
            'active_classes' => 'ring-2 ring-green-300',
            'icon' => ['M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
        ],
    ],

];
