<?php

namespace App\Modules\Notifications\Services;

use Illuminate\Support\Facades\Route;

class NotificationSourceResolver
{
    public function url(string $sourceType, int $id): ?string
    {
        $route = match ($sourceType) {
            'ops_task' => 'tasks.items.show',
            'ops_work_order' => 'tasks.work-orders.show',
            'inventory_stock_lot' => 'inventory.stock.balances',
            'livestock_withdrawal_period' => 'livestock.dashboard',
            'asset_maintenance_schedule' => 'assets.maintenance-schedules.show',
            'asset_breakdown_record' => 'assets.breakdowns.show',
            'irrigation_schedule' => 'irrigation.schedules.show',
            'irrigation_issue' => 'irrigation.issues.show',
            'finance_cost_entry' => 'finance.cost-entries.show',
            default => null,
        };

        if (! $route || ! Route::has($route)) {
            return null;
        }

        return match ($sourceType) {
            'inventory_stock_lot', 'livestock_withdrawal_period' => route($route),
            default => route($route, $id),
        };
    }
}
