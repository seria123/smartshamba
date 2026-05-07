<?php

namespace App\Modules\Assets\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Assets\Http\Controllers\Concerns\ValidatesAssetScope;
use App\Modules\Assets\Models\Asset;
use App\Modules\Assets\Models\AssetBreakdownRecord;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\UsersPermissions\Models\OrganizationMembership;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BreakdownRecordController extends Controller
{
    use ValidatesAssetScope;

    public function index(): View { return view('assets::breakdowns.index', ['breakdowns' => AssetBreakdownRecord::with(['asset', 'reportedByWorker'])->orderByDesc('breakdown_date')->paginate(20)]); }
    public function create(): View { return view('assets::breakdowns.form', $this->formData()); }
    public function show(AssetBreakdownRecord $breakdown): View { return view('assets::breakdowns.show', ['breakdown' => $breakdown->load(['organization', 'farm', 'asset', 'relatedTask', 'reportedByUser', 'reportedByWorker', 'resolvedByUser'])]); }
    public function edit(AssetBreakdownRecord $breakdown): View { return view('assets::breakdowns.form', $this->formData($breakdown)); }

    public function store(Request $request): RedirectResponse
    {
        $breakdown = AssetBreakdownRecord::query()->create($this->validated($request) + ['breakdown_number' => $this->nextNumber('ABD'), 'created_by' => $request->user()?->id]);
        if (in_array($breakdown->status, ['open', 'in_progress'], true)) {
            $breakdown->asset->update(['status' => 'broken_down']);
        }
        return redirect()->route('assets.breakdowns.index')->with('status', 'Breakdown recorded.');
    }

    public function update(Request $request, AssetBreakdownRecord $breakdown): RedirectResponse
    {
        $breakdown->update($this->validated($request) + ['updated_by' => $request->user()?->id]);
        return redirect()->route('assets.breakdowns.show', $breakdown)->with('status', 'Breakdown updated.');
    }

    public function resolve(Request $request, AssetBreakdownRecord $breakdown): RedirectResponse
    {
        $breakdown->update(['status' => 'resolved', 'resolved_by_user_id' => $request->user()?->id, 'resolved_at' => now(), 'resolution_notes' => $request->input('resolution_notes')]);
        if ($breakdown->asset->status === 'broken_down') {
            $breakdown->asset->update(['status' => 'active']);
        }
        return redirect()->route('assets.breakdowns.show', $breakdown)->with('status', 'Breakdown resolved.');
    }

    private function formData(?AssetBreakdownRecord $breakdown = null): array
    {
        return ['breakdown' => $breakdown, 'organizations' => Organization::orderBy('name')->get(), 'farms' => Farm::orderBy('name')->get(), 'assets' => Asset::orderBy('asset_code')->get(), 'tasks' => OpsTask::orderBy('task_number')->get(), 'workers' => LabourWorker::orderBy('name')->get(), 'memberships' => OrganizationMembership::with('user')->where('status', 'active')->get()];
    }

    private function validated(Request $request): array
    {
        $validator = validator($request->all(), [
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'farm_id' => ['required', 'integer', 'exists:farms,id'],
            'asset_id' => ['required', 'integer', 'exists:assets,id'],
            'related_task_id' => ['nullable', 'integer', 'exists:ops_tasks,id'],
            'breakdown_date' => ['required', 'date'],
            'issue_type' => ['required', 'in:mechanical_failure,electrical_failure,leakage,wear_and_tear,accident_damage,blocked_component,overheating,low_pressure,engine_issue,other'],
            'severity' => ['required', 'in:low,medium,high,critical'],
            'status' => ['required', 'in:open,in_progress,resolved,cancelled'],
            'description' => ['required', 'string'],
            'reported_by_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'reported_by_worker_id' => ['nullable', 'integer', 'exists:labour_workers,id'],
        ]);
        $this->addAssetScopeValidation($validator);
        $this->addFarmScopedValidation($validator, ['related_task_id' => 'ops_tasks', 'reported_by_worker_id' => 'labour_workers']);
        $this->addUserScopeValidation($validator, 'reported_by_user_id');
        return $validator->validate();
    }
}
