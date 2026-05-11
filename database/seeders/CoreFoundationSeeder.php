<?php

namespace Database\Seeders;

use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\ModuleRegistry;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Paddock;
use App\Modules\Core\Models\Site;
use App\Modules\Core\Models\Warehouse;
use Illuminate\Database\Seeder;

class CoreFoundationSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedModuleRegistry();
        $this->seedDemoCoreRecords();
    }

    private function seedModuleRegistry(): void
    {
        $modules = [
            ['core', 'Core', 'Shared organization, farm, site, and module activation foundation.', 'active'],
            ['users-permissions', 'Users and Permissions', 'Authentication, roles, permissions, and access control.', 'planned'],
            ['workers', 'Workers', 'Worker and staff records.', 'planned'],
            ['tasks', 'Tasks', 'Operational tasks and work planning.', 'planned'],
            ['inventory', 'Inventory', 'Inputs, stock, storage, and movement workflows.', 'planned'],
            ['crops', 'Crops', 'Crop production workflows.', 'planned'],
            ['livestock', 'Livestock', 'Livestock workflows.', 'planned'],
            ['irrigation', 'Irrigation', 'Water sources, zones, schedules, and logs.', 'planned'],
            ['finance-costing', 'Finance / Costing', 'Management costing, cost allocation, and farm cost summaries.', 'active'],
            ['sales-produce-revenue', 'Sales / Revenue', 'Management revenue records for produce, livestock, and other farm sales.', 'active'],
            ['sales-traceability', 'Sales and Traceability', 'Sales, batches, customers, and traceability.', 'planned'],
            ['reports', 'Reports / Analytics', 'Read-only reporting, dashboards, and farm performance analytics.', 'active'],
            ['notifications-alerts', 'Notifications / Alerts', 'In-app notifications, alerts, and reminders for farm operations.', 'active'],
            ['documents-attachments', 'Documents / Attachments', 'Private documents, attachments, and media evidence for farm records.', 'active'],
            ['audit', 'Audit Trail', 'Tracks user and system activity across the farm platform.', 'active'],
            ['settings', 'Settings', 'Platform and organization configuration.', 'planned'],
        ];

        foreach ($modules as $index => [$key, $name, $description, $status]) {
            ModuleRegistry::query()->updateOrCreate(
                ['key' => $key],
                [
                    'name' => $name,
                    'description' => $description,
                    'status' => $status,
                    'sort_order' => $index + 1,
                    'activated_at' => $status === 'active' ? now() : null,
                ],
            );
        }
    }

    private function seedDemoCoreRecords(): void
    {
        $organization = Organization::query()->firstOrCreate(
            ['slug' => 'demo-farm-organization'],
            [
                'name' => 'Demo Farm Organization',
                'status' => 'active',
            ],
        );

        $farm = Farm::query()->firstOrCreate(
            [
                'organization_id' => $organization->id,
                'name' => 'Demo Mixed Farm',
            ],
            [
                'code' => 'DMF',
                'status' => 'active',
            ],
        );

        $site = Site::query()->firstOrCreate(
            [
                'farm_id' => $farm->id,
                'name' => 'Main Production Site',
            ],
            [
                'organization_id' => $organization->id,
                'code' => 'MAIN',
                'description' => 'Demo production site for local foundation testing.',
                'status' => 'active',
            ],
        );

        Field::query()->firstOrCreate(
            [
                'farm_id' => $farm->id,
                'name' => 'North Field',
            ],
            [
                'organization_id' => $organization->id,
                'site_id' => $site->id,
                'code' => 'FIELD-N',
                'area' => 12.50,
                'area_unit' => 'acres',
                'status' => 'active',
            ],
        );

        Paddock::query()->firstOrCreate(
            [
                'farm_id' => $farm->id,
                'name' => 'East Paddock',
            ],
            [
                'organization_id' => $organization->id,
                'site_id' => $site->id,
                'code' => 'PAD-E',
                'area' => 8.00,
                'area_unit' => 'acres',
                'status' => 'active',
            ],
        );

        Warehouse::query()->firstOrCreate(
            [
                'farm_id' => $farm->id,
                'name' => 'Main Store',
            ],
            [
                'organization_id' => $organization->id,
                'site_id' => $site->id,
                'code' => 'STORE-1',
                'description' => 'Demo storage location without stock movement.',
                'status' => 'active',
            ],
        );
    }
}
