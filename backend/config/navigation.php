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
*/

return [

    // Shown on the public / front-end side of the app.
    'frontend' => [
        [
            'key' => 'tweezzo',
            'label' => 'Tweezzo',
            'url' => 'https://app.tweezzo.org',
            'image' => 'images/tweezzo-64px.png',
            'classes' => 'bg-cyan-600 text-white hover:bg-cyan-700',
        ],
        [
            'key' => 'phpmyadmin',
            'label' => 'phpMyAdmin',
            'url' => 'http://localhost/phpmyadmin',
            'image' => 'images/phpmyadmin-64px.png',
            'classes' => 'bg-teal-600 text-white hover:bg-teal-700',
        ],
        [
            'key' => 'server',
            'label' => 'Server',
            'image' => 'images/servers-64px.png',
            'classes' => 'bg-green-400 text-white hover:bg-green-500',
            'children' => [
                [
                    'key' => 'hosting-com',
                    'label' => 'Hosting.com',
                    'url' => 'https://my.hosting.com/login',
                    'image' => 'images/hosting.com-64px.png',
                ],
                [
                    'key' => 'contabo',
                    'label' => 'Contabo.com',
                    'url' => 'https://my.contabo.com/account/login',
                    'image' => 'images/contabo-64px.png',
                ],
                [
                    'key' => 'cloud-mu',
                    'label' => 'Cloud.mu',
                    'url' => 'https://my.cloud.mu/index.php?rp=/login',
                    'image' => 'images/cloud.mu-64px.png',
                ],
            ],
        ],
        [
            'key' => 'hms',
            'label' => 'HMS',
            'url' => 'https://hms.netsiteweaver.com',
            'image' => 'images/hms-64px.png',
            'classes' => 'bg-blue-600 text-white hover:bg-blue-700',
        ],
        [
            'key' => 'github',
            'label' => 'GitHub',
            'url' => 'https://github.com',
            'image' => 'images/github-64px.png',
            'image_classes' => 'bg-white rounded-sm p-0.5',
            'classes' => 'border-2 border-gray-800 text-gray-800 hover:bg-gray-800 hover:text-white dark:border-gray-200 dark:text-gray-200 dark:hover:bg-gray-200 dark:hover:text-gray-900',
        ],
        [
            'key' => 'gitlab',
            'label' => 'GitLab',
            'url' => 'https://gitlab.com',
            'image' => 'images/gitlab-64px.png',
            'classes' => 'bg-orange-600 text-white hover:bg-orange-700 dark:bg-orange-100 dark:text-orange-900 dark:hover:bg-orange-200 dark:ring-1 dark:ring-orange-300',
        ],
        [
            'key' => 'whatsapp',
            'label' => 'WhatsApp',
            'url' => 'https://web.whatsapp.com/',
            'image' => 'images/whatsapp-64px.png',
            'classes' => 'bg-green-500 text-white hover:bg-green-600',
        ],
    ],

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
