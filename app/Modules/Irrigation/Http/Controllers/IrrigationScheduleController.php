<?php

namespace App\Modules\Irrigation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use App\Modules\Crops\Models\CropCycle;
use App\Modules\Irrigation\Http\Controllers\Concerns\ValidatesIrrigationScope;
use App\Modules\Irrigation\Models\IrrigationSchedule;
use App\Modules\Irrigation\Models\IrrigationZone;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class IrrigationScheduleController extends Controller
{
    use ValidatesIrrigationScope;

    public function index(): View { return view('irrigation::schedules.index', ['schedules' => IrrigationSchedule::with(['zone', 'field', 'cropCycle', 'assignedWorker', 'assignedTeam', 'assignedUser'])->latest('scheduled_date')->paginate(20)]); }
    public function create(): View { return view('irrigation::schedules.form', $this->formData()); }
    public function show(IrrigationSchedule $schedule): View { return view('irrigation::schedules.show', ['schedule' => $schedule->load(['organization', 'farm', 'zone', 'field', 'cropCycle', 'relatedTask', 'assignedUser', 'assignedWorker', 'assignedTeam', 'events'])]); }
    public function edit(IrrigationSchedule $schedule): View { return view('irrigation::schedules.form', $this->formData($schedule)); }
    public function store(Request $request): RedirectResponse { IrrigationSchedule::query()->create($this->validated($request) + ['schedule_number' => $this->nextNumber('IRR-SCH'), 'created_by' => $request->user()->id]); return redirect()->route('irrigation.schedules.index')->with('status', 'Irrigation schedule created.'); }
    public function update(Request $request, IrrigationSchedule $schedule): RedirectResponse { $schedule->update($this->validated($request) + ['updated_by' => $request->user()->id]); return redirect()->route('irrigation.schedules.show', $schedule)->with('status', 'Irrigation schedule updated.'); }
    public function cancel(Request $request, IrrigationSchedule $schedule): RedirectResponse { $schedule->update(['status' => 'cancelled', 'cancelled_by' => $request->user()->id, 'cancelled_at' => now(), 'cancellation_reason' => $request->input('cancellation_reason')]); return redirect()->route('irrigation.schedules.show', $schedule)->with('status', 'Irrigation schedule cancelled.'); }
    public function complete(IrrigationSchedule $schedule): RedirectResponse { $schedule->update(['status' => 'completed', 'completed_at' => now()]); return redirect()->route('irrigation.schedules.show', $schedule)->with('status', 'Irrigation schedule completed.'); }

    private function formData(?IrrigationSchedule $schedule = null): array
    {
        return ['schedule' => $schedule, 'organizations' => Organization::orderBy('name')->get(), 'farms' => Farm::orderBy('name')->get(), 'zones' => IrrigationZone::orderBy('name')->get(), 'fields' => Field::orderBy('name')->get(), 'cycles' => CropCycle::orderBy('name')->get(), 'tasks' => OpsTask::orderBy('title')->get(), 'users' => User::orderBy('name')->get(), 'workers' => LabourWorker::orderBy('name')->get(), 'teams' => LabourTeam::orderBy('name')->get()];
    }

    private function validated(Request $request): array
    {
        $validator = validator($request->all(), [
            'organization_id' => ['required', 'integer', Rule::exists('organizations', 'id')],
            'farm_id' => ['required', 'integer', Rule::exists('farms', 'id')->where('organization_id', $request->input('organization_id'))],
            'irrigation_zone_id' => ['required', 'integer', Rule::exists('irrigation_zones', 'id')],
            'field_id' => ['nullable', 'integer', Rule::exists('fields', 'id')],
            'crop_cycle_id' => ['nullable', 'integer', Rule::exists('crop_cycles', 'id')],
            'related_task_id' => ['nullable', 'integer', Rule::exists('ops_tasks', 'id')],
            'scheduled_date' => ['required', 'date'],
            'scheduled_start_time' => ['nullable', 'date_format:H:i'],
            'scheduled_end_time' => ['nullable', 'date_format:H:i'],
            'planned_duration_minutes' => ['nullable', 'integer', 'min:1'],
            'planned_water_volume' => ['nullable', 'numeric', 'min:0'],
            'water_volume_unit' => ['nullable', 'string', 'max:50'],
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'status' => ['required', Rule::in(['planned', 'assigned', 'in_progress', 'completed', 'missed', 'cancelled'])],
            'assigned_user_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'assigned_worker_id' => ['nullable', 'integer', Rule::exists('labour_workers', 'id')],
            'assigned_team_id' => ['nullable', 'integer', Rule::exists('labour_teams', 'id')],
            'instructions' => ['nullable', 'string'],
        ]);
        $this->addFarmScopedValidation($validator, ['irrigation_zone_id' => 'irrigation_zones', 'field_id' => 'fields', 'crop_cycle_id' => 'crop_cycles', 'related_task_id' => 'ops_tasks', 'assigned_worker_id' => 'labour_workers', 'assigned_team_id' => 'labour_teams']);
        $this->addUserScopeValidation($validator, 'assigned_user_id');

        return $validator->validate();
    }
}
