<?php

namespace App\Modules\Assets\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Assets\Http\Controllers\Concerns\ValidatesAssetScope;
use App\Modules\Assets\Models\Asset;
use App\Modules\Assets\Models\AssetCategory;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Paddock;
use App\Modules\Core\Models\Site;
use App\Modules\Core\Models\Warehouse;
use App\Modules\UsersPermissions\Models\OrganizationMembership;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AssetController extends Controller
{
    use ValidatesAssetScope;

    public function index(): View { return view('assets::items.index', ['assets' => Asset::with(['category', 'farm', 'site', 'field', 'paddock', 'warehouse'])->orderBy('asset_code')->paginate(20)]); }
    public function create(): View { return view('assets::items.form', $this->formData()); }
    public function show(Asset $asset): View { return view('assets::items.show', ['asset' => $asset->load(['organization', 'farm', 'category', 'site', 'field', 'paddock', 'warehouse', 'assignedWorker', 'assignedUser', 'maintenanceSchedules', 'maintenanceRecords', 'breakdownRecords', 'usageRecords'])]); }
    public function edit(Asset $asset): View { return view('assets::items.form', $this->formData($asset)); }

    public function store(Request $request): RedirectResponse
    {
        Asset::query()->create($this->validated($request) + ['created_by' => $request->user()?->id]);
        return redirect()->route('assets.items.index')->with('status', 'Asset created.');
    }

    public function update(Request $request, Asset $asset): RedirectResponse
    {
        $asset->update($this->validated($request, $asset) + ['updated_by' => $request->user()?->id]);
        return redirect()->route('assets.items.show', $asset)->with('status', 'Asset updated.');
    }

    public function deactivate(Request $request, Asset $asset): RedirectResponse
    {
        $asset->update(['status' => 'inactive', 'updated_by' => $request->user()?->id]);
        return redirect()->route('assets.items.show', $asset)->with('status', 'Asset deactivated.');
    }

    public function status(Request $request, Asset $asset): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:active,in_use,under_maintenance,broken_down,retired,sold,lost,inactive,archived']]);
        $asset->update($data + ['updated_by' => $request->user()?->id]);
        return redirect()->route('assets.items.show', $asset)->with('status', 'Asset status updated.');
    }

    private function formData(?Asset $asset = null): array
    {
        return [
            'asset' => $asset,
            'organizations' => Organization::query()->orderBy('name')->get(),
            'farms' => Farm::query()->orderBy('name')->get(),
            'categories' => AssetCategory::query()->where('status', 'active')->orderBy('name')->get(),
            'sites' => Site::query()->orderBy('name')->get(),
            'fields' => Field::query()->orderBy('name')->get(),
            'paddocks' => Paddock::query()->orderBy('name')->get(),
            'warehouses' => Warehouse::query()->orderBy('name')->get(),
            'workers' => LabourWorker::query()->orderBy('name')->get(),
            'memberships' => OrganizationMembership::with('user')->where('status', 'active')->get(),
        ];
    }

    private function validated(Request $request, ?Asset $asset = null): array
    {
        $validator = validator($request->all(), [
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'farm_id' => ['required', 'integer', 'exists:farms,id'],
            'category_id' => ['required', 'integer', 'exists:asset_categories,id'],
            'asset_code' => ['required', 'string', 'max:255', Rule::unique('assets', 'asset_code')->where('farm_id', $request->integer('farm_id'))->ignore($asset?->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'asset_type' => ['required', 'in:vehicle,tractor,machinery,pump,irrigation_equipment,tool,generator,storage_equipment,building,infrastructure,livestock_equipment,crop_equipment,general_equipment,other'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_cost' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'current_value' => ['nullable', 'numeric', 'min:0'],
            'site_id' => ['nullable', 'integer', 'exists:sites,id'],
            'field_id' => ['nullable', 'integer', 'exists:fields,id'],
            'paddock_id' => ['nullable', 'integer', 'exists:paddocks,id'],
            'warehouse_id' => ['nullable', 'integer', 'exists:warehouses,id'],
            'assigned_worker_id' => ['nullable', 'integer', 'exists:labour_workers,id'],
            'assigned_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['required', 'in:active,in_use,under_maintenance,broken_down,retired,sold,lost,inactive,archived'],
            'condition_status' => ['required', 'in:excellent,good,fair,poor,critical,unknown'],
            'acquisition_source' => ['nullable', 'in:purchased,leased,rented,donated,transferred_in,other'],
            'warranty_expiry_date' => ['nullable', 'date'],
            'last_service_date' => ['nullable', 'date'],
            'next_service_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);
        $this->addFarmScopedValidation($validator, ['site_id' => 'sites', 'field_id' => 'fields', 'paddock_id' => 'paddocks', 'warehouse_id' => 'warehouses', 'assigned_worker_id' => 'labour_workers']);
        $this->addUserScopeValidation($validator, 'assigned_user_id');

        return $validator->validate();
    }
}
