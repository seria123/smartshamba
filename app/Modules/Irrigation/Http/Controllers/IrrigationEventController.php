<?php

namespace App\Modules\Irrigation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use App\Modules\Crops\Models\CropCycle;
use App\Modules\Irrigation\Http\Controllers\Concerns\ValidatesIrrigationScope;
use App\Modules\Irrigation\Models\IrrigationEvent;
use App\Modules\Irrigation\Models\IrrigationSchedule;
use App\Modules\Irrigation\Models\IrrigationWaterSource;
use App\Modules\Irrigation\Models\IrrigationZone;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class IrrigationEventController extends Controller
{
    use ValidatesIrrigationScope;

    public function index(): View { return view('irrigation::events.index', ['events' => IrrigationEvent::with(['zone', 'field', 'performedByWorker', 'team', 'performedByUser'])->latest('irrigation_date')->paginate(20)]); }
    public function create(): View { return view('irrigation::events.form', $this->formData()); }
    public function show(IrrigationEvent $event): View { return view('irrigation::events.show', ['event' => $event->load(['organization', 'farm', 'zone', 'waterSource', 'field', 'cropCycle', 'schedule', 'relatedTask', 'performedByUser', 'performedByWorker', 'team'])]); }
    public function edit(IrrigationEvent $event): View { return view('irrigation::events.form', $this->formData($event)); }
    public function store(Request $request): RedirectResponse { IrrigationEvent::query()->create($this->validated($request) + ['event_number' => $this->nextNumber('IRR-EVT'), 'created_by' => $request->user()->id]); return redirect()->route('irrigation.events.index')->with('status', 'Irrigation event recorded.'); }
    public function update(Request $request, IrrigationEvent $event): RedirectResponse { $event->update($this->validated($request) + ['updated_by' => $request->user()->id]); return redirect()->route('irrigation.events.show', $event)->with('status', 'Irrigation event updated.'); }
    public function cancel(Request $request, IrrigationEvent $event): RedirectResponse { $event->update(['status' => 'cancelled', 'cancelled_by' => $request->user()->id, 'cancelled_at' => now(), 'cancellation_reason' => $request->input('cancellation_reason')]); return redirect()->route('irrigation.events.show', $event)->with('status', 'Irrigation event cancelled.'); }

    private function formData(?IrrigationEvent $event = null): array
    {
        return ['event' => $event, 'organizations' => Organization::orderBy('name')->get(), 'farms' => Farm::orderBy('name')->get(), 'zones' => IrrigationZone::orderBy('name')->get(), 'sources' => IrrigationWaterSource::orderBy('name')->get(), 'fields' => Field::orderBy('name')->get(), 'cycles' => CropCycle::orderBy('name')->get(), 'schedules' => IrrigationSchedule::orderBy('schedule_number')->get(), 'tasks' => OpsTask::orderBy('title')->get(), 'users' => User::orderBy('name')->get(), 'workers' => LabourWorker::orderBy('name')->get(), 'teams' => LabourTeam::orderBy('name')->get()];
    }

    private function validated(Request $request): array
    {
        $validator = validator($request->all(), [
            'organization_id' => ['required', 'integer', Rule::exists('organizations', 'id')],
            'farm_id' => ['required', 'integer', Rule::exists('farms', 'id')->where('organization_id', $request->input('organization_id'))],
            'irrigation_zone_id' => ['required', 'integer', Rule::exists('irrigation_zones', 'id')],
            'water_source_id' => ['nullable', 'integer', Rule::exists('irrigation_water_sources', 'id')],
            'field_id' => ['nullable', 'integer', Rule::exists('fields', 'id')],
            'crop_cycle_id' => ['nullable', 'integer', Rule::exists('crop_cycles', 'id')],
            'schedule_id' => ['nullable', 'integer', Rule::exists('irrigation_schedules', 'id')],
            'related_task_id' => ['nullable', 'integer', Rule::exists('ops_tasks', 'id')],
            'irrigation_date' => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'water_volume' => ['nullable', 'numeric', 'min:0'],
            'water_volume_unit' => ['nullable', 'string', 'max:50'],
            'method' => ['nullable', Rule::in(['drip', 'sprinkler', 'furrow', 'flood', 'manual', 'pivot', 'micro_sprinkler', 'other'])],
            'status' => ['required', Rule::in(['recorded', 'approved', 'cancelled'])],
            'performed_by_user_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'performed_by_worker_id' => ['nullable', 'integer', Rule::exists('labour_workers', 'id')],
            'team_id' => ['nullable', 'integer', Rule::exists('labour_teams', 'id')],
            'notes' => ['nullable', 'string'],
        ]);
        $this->addFarmScopedValidation($validator, ['irrigation_zone_id' => 'irrigation_zones', 'water_source_id' => 'irrigation_water_sources', 'field_id' => 'fields', 'crop_cycle_id' => 'crop_cycles', 'schedule_id' => 'irrigation_schedules', 'related_task_id' => 'ops_tasks', 'performed_by_worker_id' => 'labour_workers', 'team_id' => 'labour_teams']);
        $this->addUserScopeValidation($validator, 'performed_by_user_id');

        return $validator->validate();
    }
}
