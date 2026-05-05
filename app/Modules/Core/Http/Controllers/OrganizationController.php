<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Organization;
use Illuminate\Contracts\View\View;

class OrganizationController extends Controller
{
    public function index(): View
    {
        return view('core::organizations.index', [
            'organizations' => Organization::query()
                ->withCount(['farms', 'sites', 'fields', 'paddocks', 'warehouses'])
                ->orderBy('name')
                ->paginate(20),
        ]);
    }

    public function show(Organization $organization): View
    {
        return view('core::organizations.show', [
            'organization' => $organization->load(['farms', 'sites', 'fields', 'paddocks', 'warehouses']),
        ]);
    }
}
