<?php

namespace App\Modules\Assets\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Assets\Http\Controllers\Concerns\ValidatesAssetScope;
use App\Modules\Assets\Models\Asset;
use App\Modules\Assets\Models\AssetMaintenanceSchedule;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\UsersPermissions\Models\OrganizationMembership;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceScheduleController extends Controller
{
    use ValidatesAssetScope;

    public function index(): View { return view('assets::maintenance-schedules.index', ['schedules' => AssetMaintenanceSchedule::with(['asset', 'farm', 'assignedWorker', 'assignedTeam'])->orderByDesc('scheduled_date')->paginate(20)]); }
    public function create(): View { return view('assets::maintenance-schedules.form', $this->formData()); }
    public function show(AssetMaintenanceSchedule $schedule): View { return view('assets::maintenance-schedules.show', ['schedule' => $schedule->load(['organization', 'farm', 'asset', 'relatedTask', 'assignedUser', 'assignedWorker', 'assignedTeam'])]); }
    public function edit(AssetMaintenanceSchedule $schedule): View { return view('assets::maintenance-schedules.form', $this->formData($schedule)); }

    public function store(Request $request): RedirectResponse
    {
        AssetMaintenanceSchedule::query()->create($this->validated($request) + ['schedule_number' => $this->nextNumber('AMS'), 'created_by' => $request->user()?->id]);
        return redirect()->route('assets.maintenance-schedules.index')->with('status', 'Maintenance schedule created.');
    }

    public function update(Request $request, AssetMaintenanceSchedule $schedule): RedirectResponse
    {
        $schedule->update($this->validated($request) + ['updated_by' => $request->user()?->id]);
        return redirect()->route('assets.maintenance-schedules.show', $schedule)->with('status', 'Maintenance schedule updated.');
    }

    public function cancel(Request $request, AssetMaintenanceSchedule $schedule): RedirectResponse
    {
        $schedule->update(['status' => 'cancelled', 'cancelled_by' => $request->user()?->id, 'cancelled_at' => now(), 'cancellation_reason' => $request->input('cancellation_reason')]);
        return redirect()->route('assets.maintenance-schedules.show', $schedule)->with('status', 'Maintenance schedule cancelled.');
    }

    public function complete(Request $request, AssetMaintenanceSchedule $schedule): RedirectResponse
    {
        $schedule->update(['status' => 'completed', 'completed_at' => now(), 'updated_by' => $request->user()?->id]);
        return redirect()->route('assets.maintenance-schedules.show', $schedule)->with('status', 'Maintenance schedule completed.');
    }

    private function formData(?AssetMaintenanceSchedule $schedule = null): array
    {
        return ['schedule' => $schedule, 'organizations' => Organization::orderBy('name')->get(), 'farms' => Farm::orderBy('name')->get(), 'assets' => Asset::orderBy('asset_code')->get(), 'tasks' => OpsTask::orderBy('task_number')->get(), 'workers' => LabourWorker::orderBy('name')->get(), 'teams' => LabourTeam::orderBy('name')->get(), 'memberships' => OrganizationMembership::with('user')->where('status', 'active')->get()];
    }

    private function validated(Request $request): array
    {
        $validator = validator($request->all(), [
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'farm_id' => ['required', 'integer', 'exists:farms,id'],
            'asset_id' => ['required', 'integer', 'exists:assets,id'],
            'maintenance_type' => ['required', 'in:routine_service,inspection,repair,calibration,cleaning,lubrication,replacement,safety_check,other'],
            'scheduled_date' => ['required', 'date'],
            'frequency_type' => ['nullable', 'in:none,daily,weekly,monthly,quarterly,annually,usage_based,custom'],
            'frequency_interval' => ['nullable', 'integer', 'min:1'],
            'priority' => ['required', 'in:low,normal,high,urgent'],
            'status' => ['required', 'in:planned,assigned,in_progress,completed,missed,cancelled'],
            'assigned_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'assigned_worker_id' => ['nullable', 'integer', 'exists:labour_workers,id'],
            'assigned_team_id' => ['nullable', 'integer', 'exists:labour_teams,id'],
            'related_task_id' => ['nullable', 'integer', 'exists:ops_tasks,id'],
            'instructions' => ['nullable', 'string'],
        ]);
        $this->addAssetScopeValidation($validator);
        $this->addFarmScopedValidation($validator, ['related_task_id' => 'ops_tasks', 'assigned_worker_id' => 'labour_workers', 'assigned_team_id' => 'labour_teams']);
        $this->addUserScopeValidation($validator, 'assigned_user_id');
        return $validator->validate();
    }
}
