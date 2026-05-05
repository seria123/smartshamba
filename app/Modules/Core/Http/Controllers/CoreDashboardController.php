<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\ModuleRegistry;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Paddock;
use App\Modules\Core\Models\Site;
use App\Modules\Core\Models\Warehouse;
use Illuminate\Contracts\View\View;

class CoreDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('core::dashboard', [
            'counts' => [
                'Organizations' => Organization::count(),
                'Farms' => Farm::count(),
                'Sites' => Site::count(),
                'Fields' => Field::count(),
                'Paddocks' => Paddock::count(),
                'Warehouses' => Warehouse::count(),
                'Modules' => ModuleRegistry::count(),
            ],
        ]);
    }
}
