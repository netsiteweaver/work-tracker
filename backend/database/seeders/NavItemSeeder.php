<?php

namespace Database\Seeders;

use App\Models\NavItem;
use Illuminate\Database\Seeder;

/**
 * The top menu buttons a fresh install starts with. They are editable from the
 * back office afterwards (see App\Models\NavItem), so this runs once and then
 * leaves the table alone: an item whose key already exists is skipped rather
 * than reset, and anything added or deleted since stays as it is.
 */
class NavItemSeeder extends Seeder
{
    /**
     * @var list<array<string, mixed>>
     */
    protected array $items = [
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
    ];

    public function run(): void
    {
        foreach (array_values($this->items) as $sort => $item) {
            $parent = $this->create($item, null, $sort);

            foreach (array_values($item['children'] ?? []) as $childSort => $child) {
                $this->create($child, $parent->id, $childSort);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $item
     */
    protected function create(array $item, ?int $parentId, int $sort): NavItem
    {
        return NavItem::firstOrCreate(
            ['key' => $item['key']],
            [
                'parent_id' => $parentId,
                'label' => $item['label'],
                'url' => $item['url'] ?? null,
                'image' => $item['image'] ?? null,
                'classes' => $item['classes'] ?? null,
                'image_classes' => $item['image_classes'] ?? null,
                'sort_order' => $sort,
                'is_active' => true,
            ]
        );
    }
}
