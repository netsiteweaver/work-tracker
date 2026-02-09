<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            [
                'name' => 'E-commerce Platform Redesign',
                'description' => 'Complete redesign of the e-commerce platform with modern UI/UX',
                'dev_path' => '/var/www/ecommerce-dev',
                'staging_url' => 'https://staging.ecommerce.example.com',
                'production_url' => 'https://ecommerce.example.com',
                'status' => 'in_progress',
                'start_date' => '2026-01-15',
                'finish_date' => null,
                'sort_order' => 1,
            ],
            [
                'name' => 'Mobile App Development',
                'description' => 'Native iOS and Android app for customer engagement',
                'dev_path' => '/var/www/mobile-app',
                'staging_url' => null,
                'production_url' => null,
                'status' => 'new',
                'start_date' => null,
                'finish_date' => null,
                'sort_order' => 2,
            ],
            [
                'name' => 'CRM Integration',
                'description' => 'Integrate Salesforce CRM with existing systems',
                'dev_path' => '/var/www/crm-integration',
                'staging_url' => 'https://staging.crm.example.com',
                'production_url' => 'https://crm.example.com',
                'status' => 'completed',
                'start_date' => '2025-11-01',
                'finish_date' => '2026-01-30',
                'sort_order' => 3,
            ],
            [
                'name' => 'Legacy System Migration',
                'description' => 'Migrate legacy system to modern cloud infrastructure',
                'dev_path' => '/var/www/migration',
                'staging_url' => null,
                'production_url' => null,
                'status' => 'stopped',
                'start_date' => '2025-10-01',
                'finish_date' => null,
                'sort_order' => 4,
            ],
            [
                'name' => 'Dashboard Analytics',
                'description' => 'Real-time analytics dashboard for business intelligence',
                'dev_path' => '/var/www/analytics-dashboard',
                'staging_url' => 'https://staging.analytics.example.com',
                'production_url' => null,
                'status' => 'in_progress',
                'start_date' => '2026-01-20',
                'finish_date' => null,
                'sort_order' => 5,
            ],
            [
                'name' => 'API Gateway',
                'description' => 'Centralized API gateway for microservices',
                'dev_path' => '/var/www/api-gateway',
                'staging_url' => null,
                'production_url' => null,
                'status' => 'new',
                'start_date' => null,
                'finish_date' => null,
                'sort_order' => 6,
            ],
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }
    }
}
