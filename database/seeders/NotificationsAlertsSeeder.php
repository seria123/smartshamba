<?php

namespace Database\Seeders;

use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Notifications\Models\NotificationRule;
use Illuminate\Database\Seeder;

class NotificationsAlertsSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::query()->first();

        if (! $organization) {
            return;
        }

        $farm = Farm::query()->where('organization_id', $organization->id)->first();

        $rules = [
            ['inventory-low-stock', 'Inventory low stock', 'Inventory lots at or below reorder level.', 'inventory', 'inventory.low_stock', 'high', null],
            ['inventory-expiry-soon', 'Inventory lot expiry soon', 'Inventory lots expiring within the configured window.', 'inventory', 'inventory.expiry_soon', 'medium', ['days' => 30]],
            ['tasks-overdue', 'Task or work order overdue', 'Tasks and work orders past their due date.', 'tasks', 'tasks.overdue', 'high', null],
            ['livestock-withdrawal-active', 'Livestock withdrawal active', 'Active livestock withdrawal periods.', 'livestock', 'livestock.withdrawal_active', 'high', null],
            ['assets-maintenance-due', 'Asset maintenance due', 'Asset maintenance scheduled soon or overdue.', 'assets', 'assets.maintenance_due', 'medium', ['days' => 7]],
            ['assets-breakdown-open', 'Asset breakdown open', 'Asset breakdowns that are not resolved.', 'assets', 'assets.breakdown_open', 'high', null],
            ['irrigation-schedule-due', 'Irrigation schedule due', 'Irrigation schedules due soon or overdue.', 'irrigation', 'irrigation.schedule_due', 'medium', ['days' => 3]],
            ['irrigation-issue-open', 'Irrigation issue open', 'Irrigation issues that are not resolved.', 'irrigation', 'irrigation.issue_open', 'high', null],
            ['finance-unconfirmed-cost', 'Finance cost awaiting confirmation', 'Draft or pending cost entries.', 'finance', 'finance.unconfirmed_cost', 'medium', null],
        ];

        foreach ($rules as [$code, $name, $description, $sourceModule, $signalType, $severity, $settings]) {
            NotificationRule::query()->updateOrCreate(
                [
                    'organization_id' => $organization->id,
                    'farm_id' => $farm?->id,
                    'code' => $code,
                ],
                [
                    'name' => $name,
                    'description' => $description,
                    'source_module' => $sourceModule,
                    'signal_type' => $signalType,
                    'severity' => $severity,
                    'is_active' => true,
                    'settings' => $settings,
                ],
            );
        }
    }
}
