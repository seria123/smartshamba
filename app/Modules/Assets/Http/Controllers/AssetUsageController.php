<?php

namespace App\Modules\Assets\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Assets\Http\Controllers\Concerns\ValidatesAssetScope;
use App\Modules\Assets\Models\Asset;
use App\Modules\Assets\Models\AssetUsageRecord;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Paddock;
use App\Modules\Core\Models\Warehouse;
use App\Modules\Irrigation\Models\IrrigationEvent;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\UsersPermissions\Models\OrganizationMembership;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetUsageController extends Controller
{
    use ValidatesAssetScope;

    public function index(): View { return view('assets::usage.index', ['records' => AssetUsageRecord::with(['asset', 'relatedTask', 'relatedIrrigationEvent', 'usedByWorker', 'team'])->orderByDesc('usage_date')->paginate(20)]); }
    public function create(): View { return view('assets::usage.form', $this->formData()); }

    public function store(Request $request): RedirectResponse
    {
        AssetUsageRecord::query()->create($this->validated($request) + ['created_by' => $request->user()?->id]);
        return redirect()->route('assets.usage.index')->with('status', 'Asset usage recorded.');
    }

    private function formData(): array
    {
        return ['organizations' => Organization::orderBy('name')->get(), 'farms' => Farm::orderBy('name')->get(), 'assets' => Asset::orderBy('asset_code')->get(), 'tasks' => OpsTask::orderBy('task_number')->get(), 'events' => IrrigationEvent::orderByDesc('irrigation_date')->get(), 'fields' => Field::orderBy('name')->get(), 'paddocks' => Paddock::orderBy('name')->get(), 'warehouses' => Warehouse::orderBy('name')->get(), 'workers' => LabourWorker::orderBy('name')->get(), 'teams' => LabourTeam::orderBy('name')->get(), 'memberships' => OrganizationMembership::with('user')->where('status', 'active')->get()];
    }

    private function validated(Request $request): array
    {
        $validator = validator($request->all(), [
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'farm_id' => ['required', 'integer', 'exists:farms,id'],
            'asset_id' => ['required', 'integer', 'exists:assets,id'],
            'usage_date' => ['required', 'date'],
            'usage_type' => ['required', 'in:general,crop_work,livestock_work,irrigation,transport,maintenance,storage,other'],
            'related_task_id' => ['nullable', 'integer', 'exists:ops_tasks,id'],
            'related_irrigation_event_id' => ['nullable', 'integer', 'exists:irrigation_events,id'],
            'field_id' => ['nullable', 'integer', 'exists:fields,id'],
            'paddock_id' => ['nullable', 'integer', 'exists:paddocks,id'],
            'warehouse_id' => ['nullable', 'integer', 'exists:warehouses,id'],
            'used_by_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'used_by_worker_id' => ['nullable', 'integer', 'exists:labour_workers,id'],
            'team_id' => ['nullable', 'integer', 'exists:labour_teams,id'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],
            'meter_start' => ['nullable', 'numeric', 'min:0'],
            'meter_end' => ['nullable', 'numeric', 'min:0'],
            'usage_quantity' => ['nullable', 'numeric', 'min:0'],
            'usage_unit' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);
        $this->addAssetScopeValidation($validator);
        $this->addFarmScopedValidation($validator, ['related_task_id' => 'ops_tasks', 'related_irrigation_event_id' => 'irrigation_events', 'field_id' => 'fields', 'paddock_id' => 'paddocks', 'warehouse_id' => 'warehouses', 'used_by_worker_id' => 'labour_workers', 'team_id' => 'labour_teams']);
        $this->addUserScopeValidation($validator, 'used_by_user_id');
        return $validator->validate();
    }
}
