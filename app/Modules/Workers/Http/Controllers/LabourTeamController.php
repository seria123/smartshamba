<?php

namespace App\Modules\Workers\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Workers\Http\Controllers\Concerns\ValidatesLabourScope;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LabourTeamController extends Controller
{
    use ValidatesLabourScope;

    public function index(): View
    {
        return view('labour::teams.index', [
            'teams' => LabourTeam::query()
                ->with(['organization', 'farm', 'supervisor'])
                ->orderBy('name')
                ->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('labour::teams.form', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $team = LabourTeam::query()->create($this->validated($request));

        return redirect()->route('labour.teams.show', $team)->with('status', 'Team created.');
    }

    public function show(LabourTeam $team): View
    {
        return view('labour::teams.show', [
            'team' => $team->load(['organization', 'farm', 'supervisor', 'workers']),
            'availableWorkers' => LabourWorker::query()
                ->where('farm_id', $team->farm_id)
                ->whereDoesntHave('teams', fn ($query) => $query->whereKey($team->id))
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function edit(LabourTeam $team): View
    {
        return view('labour::teams.form', $this->formData($team));
    }

    public function update(Request $request, LabourTeam $team): RedirectResponse
    {
        $team->update($this->validated($request));

        return redirect()->route('labour.teams.show', $team)->with('status', 'Team updated.');
    }

    public function attachWorker(Request $request, LabourTeam $team): RedirectResponse
    {
        $data = $request->validate([
            'labour_worker_id' => ['required', 'integer', Rule::exists('labour_workers', 'id')],
        ]);

        $this->ensureWorkerBelongsToFarm((int) $data['labour_worker_id'], (int) $team->farm_id);

        $team->workers()->syncWithoutDetaching([
            $data['labour_worker_id'] => ['joined_at' => now()],
        ]);

        return redirect()->route('labour.teams.show', $team)->with('status', 'Worker added to team.');
    }

    public function detachWorker(LabourTeam $team, LabourWorker $worker): RedirectResponse
    {
        $this->ensureWorkerBelongsToFarm((int) $worker->id, (int) $team->farm_id);
        $team->workers()->detach($worker->id);

        return redirect()->route('labour.teams.show', $team)->with('status', 'Worker removed from team.');
    }

    public function deactivate(LabourTeam $team): RedirectResponse
    {
        $team->update(['status' => 'inactive']);

        return redirect()->route('labour.teams.show', $team)->with('status', 'Team deactivated.');
    }

    private function formData(?LabourTeam $team = null): array
    {
        return [
            'team' => $team,
            'organizations' => Organization::query()->orderBy('name')->get(),
            'farms' => Farm::query()->with('organization')->orderBy('name')->get(),
            'workers' => LabourWorker::query()->with('farm')->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'organization_id' => ['required', 'integer', Rule::exists('organizations', 'id')],
            'farm_id' => ['required', 'integer', Rule::exists('farms', 'id')],
            'supervisor_worker_id' => ['nullable', 'integer', Rule::exists('labour_workers', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255'],
            'team_type' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->ensureFarmBelongsToOrganization((int) $data['farm_id'], (int) $data['organization_id']);

        if ($data['supervisor_worker_id'] ?? null) {
            $this->ensureWorkerBelongsToFarm((int) $data['supervisor_worker_id'], (int) $data['farm_id']);
        }

        return $data;
    }
}
