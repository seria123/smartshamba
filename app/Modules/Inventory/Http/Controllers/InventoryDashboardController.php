<?php

namespace App\Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\Models\InventoryMovement;
use App\Modules\Inventory\Models\InventoryProduct;
use App\Modules\Inventory\Models\InventoryStockLot;
use App\Modules\Inventory\Models\InventorySupplier;
use Illuminate\Contracts\View\View;

class InventoryDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('inventory::dashboard', [
            'counts' => [
                'Products' => InventoryProduct::count(),
                'Suppliers' => InventorySupplier::count(),
                'Stock lots' => InventoryStockLot::count(),
                'Movements' => InventoryMovement::count(),
            ],
        ]);
    }
}
