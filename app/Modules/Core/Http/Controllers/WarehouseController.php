<?php

namespace App\Modules\Core\Http\Controllers;

use App\Modules\Core\Http\Controllers\Concerns\ManagesCoreAssets;
use App\Modules\Core\Models\Warehouse;

class WarehouseController
{
    use ManagesCoreAssets;

    protected function modelClass(): string
    {
        return Warehouse::class;
    }

    protected function routeBase(): string
    {
        return 'core.warehouses';
    }

    protected function label(): string
    {
        return 'Warehouse';
    }

    protected function allowsDescription(): bool
    {
        return true;
    }
}
