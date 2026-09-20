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
            'color' => 'cyan',
        ],
        [
            'key' => 'phpmyadmin',
            'label' => 'phpMyAdmin',
            'url' => 'http://localhost/phpmyadmin',
            'image' => 'images/phpmyadmin-64px.png',
            'color' => 'teal',
        ],
        [
            'key' => 'server',
            'label' => 'Server',
            'image' => 'images/servers-64px.png',
            'color' => 'green',
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
            'color' => 'blue',
        ],
        [
            'key' => 'github',
            'label' => 'GitHub',
            'url' => 'https://github.com',
            'image' => 'images/github-64px.png',
            'image_classes' => 'bg-white rounded-sm p-0.5',
            'color' => 'gray',
            'outline' => true,
        ],
        [
            'key' => 'gitlab',
            'label' => 'GitLab',
            'url' => 'https://gitlab.com',
            'image' => 'images/gitlab-64px.png',
            'color' => 'orange',
        ],
        [
            'key' => 'whatsapp',
            'label' => 'WhatsApp',
            'url' => 'https://web.whatsapp.com/',
            'image' => 'images/whatsapp-64px.png',
            'color' => 'green',
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
                'color' => $item['color'] ?? null,
                'outline' => $item['outline'] ?? false,
                'classes' => $item['classes'] ?? null,
                'image_classes' => $item['image_classes'] ?? null,
                'sort_order' => $sort,
                'is_active' => true,
            ]
        );
    }
}
