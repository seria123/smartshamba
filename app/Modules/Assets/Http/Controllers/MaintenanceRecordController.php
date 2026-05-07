<?php

namespace App\Modules\Assets\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Assets\Http\Controllers\Concerns\ValidatesAssetScope;
use App\Modules\Assets\Models\Asset;
use App\Modules\Assets\Models\AssetMaintenanceRecord;
use App\Modules\Assets\Models\AssetMaintenanceSchedule;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Inventory\Models\InventoryProduct;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\UsersPermissions\Models\OrganizationMembership;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceRecordController extends Controller
{
    use ValidatesAssetScope;

    public function index(): View { return view('assets::maintenance-records.index', ['records' => AssetMaintenanceRecord::with(['asset', 'performedByWorker', 'team'])->orderByDesc('maintenance_date')->paginate(20)]); }
    public function create(): View { return view('assets::maintenance-records.form', $this->formData()); }
    public function show(AssetMaintenanceRecord $record): View { return view('assets::maintenance-records.show', ['record' => $record->load(['organization', 'farm', 'asset', 'maintenanceSchedule', 'relatedTask', 'performedByUser', 'performedByWorker', 'team', 'product'])]); }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $product = ! empty($data['product_id']) ? InventoryProduct::find($data['product_id']) : null;
        $record = AssetMaintenanceRecord::query()->create($data + ['record_number' => $this->nextNumber('AMR'), 'product_name_snapshot' => $product?->name, 'created_by' => $request->user()?->id]);
        $record->asset->update(['last_service_date' => $record->maintenance_date, 'next_service_date' => $record->next_service_date ?: $record->asset->next_service_date]);
        return redirect()->route('assets.maintenance-records.show', $record)->with('status', 'Maintenance record created.');
    }

    private function formData(): array
    {
        return ['organizations' => Organization::orderBy('name')->get(), 'farms' => Farm::orderBy('name')->get(), 'assets' => Asset::orderBy('asset_code')->get(), 'schedules' => AssetMaintenanceSchedule::orderByDesc('scheduled_date')->get(), 'tasks' => OpsTask::orderBy('task_number')->get(), 'workers' => LabourWorker::orderBy('name')->get(), 'teams' => LabourTeam::orderBy('name')->get(), 'memberships' => OrganizationMembership::with('user')->where('status', 'active')->get(), 'products' => InventoryProduct::orderBy('name')->get()];
    }

    private function validated(Request $request): array
    {
        $validator = validator($request->all(), [
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'farm_id' => ['required', 'integer', 'exists:farms,id'],
            'asset_id' => ['required', 'integer', 'exists:assets,id'],
            'maintenance_schedule_id' => ['nullable', 'integer', 'exists:asset_maintenance_schedules,id'],
            'related_task_id' => ['nullable', 'integer', 'exists:ops_tasks,id'],
            'maintenance_type' => ['required', 'in:routine_service,inspection,repair,calibration,cleaning,lubrication,replacement,safety_check,other'],
            'maintenance_date' => ['required', 'date'],
            'status' => ['required', 'in:recorded,approved,cancelled'],
            'performed_by_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'performed_by_worker_id' => ['nullable', 'integer', 'exists:labour_workers,id'],
            'team_id' => ['nullable', 'integer', 'exists:labour_teams,id'],
            'service_provider' => ['nullable', 'string', 'max:255'],
            'problem_found' => ['nullable', 'string'],
            'work_done' => ['required', 'string'],
            'parts_used_notes' => ['nullable', 'string'],
            'product_id' => ['nullable', 'integer', 'exists:inventory_products,id'],
            'quantity_used' => ['nullable', 'numeric', 'min:0'],
            'quantity_unit' => ['nullable', 'string', 'max:255'],
            'external_cost' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'next_service_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);
        $this->addAssetScopeValidation($validator);
        $this->addFarmScopedValidation($validator, ['maintenance_schedule_id' => 'asset_maintenance_schedules', 'related_task_id' => 'ops_tasks', 'performed_by_worker_id' => 'labour_workers', 'team_id' => 'labour_teams']);
        $this->addUserScopeValidation($validator, 'performed_by_user_id');
        $this->addProductOrganizationValidation($validator);
        return $validator->validate();
    }
}
