<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CoreFoundationSeeder::class);
        $this->call(UsersPermissionsSeeder::class);
        $this->call(WorkersLabourSeeder::class);
        $this->call(TasksWorkOrdersSeeder::class);
        $this->call(InventoryInputsSeeder::class);
        $this->call(CropsModuleSeeder::class);
        $this->call(LivestockModuleSeeder::class);
        $this->call(IrrigationModuleSeeder::class);
        $this->call(AssetsMaintenanceSeeder::class);
        $this->call(FinanceCostingSeeder::class);
        $this->call(SalesRevenueSeeder::class);
        $this->call(NotificationsAlertsSeeder::class);
        $this->call(DocumentsAttachmentsSeeder::class);
    }
}
