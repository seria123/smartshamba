<?php

namespace App\Modules\Documents\Services;

use Illuminate\Support\Facades\Route;

class AttachmentSourceResolver
{
    public function actionUrl(?string $module, ?string $type, mixed $id): ?string
    {
        if (! $module || ! $type || ! $id) {
            return null;
        }

        $route = match ($module.'.'.$type) {
            'core.farm' => 'core.farms.show',
            'tasks.task' => 'tasks.items.show',
            'tasks.work_order' => 'tasks.work-orders.show',
            'inventory.product' => 'inventory.products.show',
            'inventory.stock_lot' => 'inventory.stock.balances',
            'crops.crop_cycle' => 'crops.cycles.show',
            'livestock.animal' => 'livestock.animals.show',
            'livestock.group' => 'livestock.groups.show',
            'irrigation.schedule' => 'irrigation.schedules.show',
            'irrigation.event' => 'irrigation.events.show',
            'irrigation.issue' => 'irrigation.issues.show',
            'assets.asset' => 'assets.items.show',
            'assets.maintenance_record' => 'assets.maintenance-records.show',
            'finance.cost_entry' => 'finance.cost-entries.show',
            'sales.sale_record' => 'sales.records.show',
            default => null,
        };

        if (! $route || ! Route::has($route)) {
            return null;
        }

        return $module.'.'.$type === 'inventory.stock_lot' ? route($route) : route($route, $id);
    }
}
