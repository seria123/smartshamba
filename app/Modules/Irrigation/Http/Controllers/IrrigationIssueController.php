<?php

namespace App\Modules\Irrigation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use App\Modules\Irrigation\Http\Controllers\Concerns\ValidatesIrrigationScope;
use App\Modules\Irrigation\Models\IrrigationIssue;
use App\Modules\Irrigation\Models\IrrigationWaterSource;
use App\Modules\Irrigation\Models\IrrigationZone;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class IrrigationIssueController extends Controller
{
    use ValidatesIrrigationScope;

    public function index(): View { return view('irrigation::issues.index', ['issues' => IrrigationIssue::with(['zone', 'waterSource', 'field'])->latest('issue_date')->paginate(20)]); }
    public function create(): View { return view('irrigation::issues.form', $this->formData()); }
    public function show(IrrigationIssue $issue): View { return view('irrigation::issues.show', ['issue' => $issue->load(['organization', 'farm', 'zone', 'waterSource', 'field', 'relatedTask', 'reportedByUser', 'reportedByWorker', 'resolvedBy'])]); }
    public function edit(IrrigationIssue $issue): View { return view('irrigation::issues.form', $this->formData($issue)); }
    public function store(Request $request): RedirectResponse { IrrigationIssue::query()->create($this->validated($request) + ['issue_number' => $this->nextNumber('IRR-ISS'), 'created_by' => $request->user()->id, 'reported_by_user_id' => $request->user()->id]); return redirect()->route('irrigation.issues.index')->with('status', 'Irrigation issue created.'); }
    public function update(Request $request, IrrigationIssue $issue): RedirectResponse { $issue->update($this->validated($request) + ['updated_by' => $request->user()->id]); return redirect()->route('irrigation.issues.show', $issue)->with('status', 'Irrigation issue updated.'); }
    public function resolve(Request $request, IrrigationIssue $issue): RedirectResponse { $issue->update(['status' => 'resolved', 'resolved_by' => $request->user()->id, 'resolved_at' => now(), 'resolution_notes' => $request->input('resolution_notes')]); return redirect()->route('irrigation.issues.show', $issue)->with('status', 'Irrigation issue resolved.'); }

    private function formData(?IrrigationIssue $issue = null): array
    {
        return ['issue' => $issue, 'organizations' => Organization::orderBy('name')->get(), 'farms' => Farm::orderBy('name')->get(), 'zones' => IrrigationZone::orderBy('name')->get(), 'sources' => IrrigationWaterSource::orderBy('name')->get(), 'fields' => Field::orderBy('name')->get(), 'tasks' => OpsTask::orderBy('title')->get(), 'workers' => LabourWorker::orderBy('name')->get()];
    }

    private function validated(Request $request): array
    {
        $validator = validator($request->all(), [
            'organization_id' => ['required', 'integer', Rule::exists('organizations', 'id')],
            'farm_id' => ['required', 'integer', Rule::exists('farms', 'id')->where('organization_id', $request->input('organization_id'))],
            'irrigation_zone_id' => ['nullable', 'integer', Rule::exists('irrigation_zones', 'id')],
            'water_source_id' => ['nullable', 'integer', Rule::exists('irrigation_water_sources', 'id')],
            'field_id' => ['nullable', 'integer', Rule::exists('fields', 'id')],
            'related_task_id' => ['nullable', 'integer', Rule::exists('ops_tasks', 'id')],
            'issue_date' => ['required', 'date'],
            'issue_type' => ['required', Rule::in(['pump_failure', 'blocked_line', 'broken_pipe', 'low_pressure', 'water_shortage', 'over_irrigation', 'under_irrigation', 'leakage', 'power_failure', 'other'])],
            'severity' => ['required', Rule::in(['low', 'medium', 'high', 'critical'])],
            'status' => ['required', Rule::in(['open', 'in_progress', 'resolved', 'cancelled'])],
            'description' => ['required', 'string'],
            'reported_by_worker_id' => ['nullable', 'integer', Rule::exists('labour_workers', 'id')],
            'resolution_notes' => ['nullable', 'string'],
        ]);
        $this->addFarmScopedValidation($validator, ['irrigation_zone_id' => 'irrigation_zones', 'water_source_id' => 'irrigation_water_sources', 'field_id' => 'fields', 'related_task_id' => 'ops_tasks', 'reported_by_worker_id' => 'labour_workers']);

        return $validator->validate();
    }
}
