<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Farm;
use Illuminate\Contracts\View\View;

class FarmController extends Controller
{
    public function index(): View
    {
        return view('core::farms.index', [
            'farms' => Farm::query()
                ->with('organization')
                ->withCount(['sites', 'fields', 'paddocks', 'warehouses'])
                ->orderBy('name')
                ->paginate(20),
        ]);
    }

    public function show(Farm $farm): View
    {
        return view('core::farms.show', [
            'farm' => $farm->load(['organization', 'sites', 'fields', 'paddocks', 'warehouses']),
        ]);
    }
}
