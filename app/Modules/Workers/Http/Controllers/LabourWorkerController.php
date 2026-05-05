<?php

namespace App\Modules\Workers\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Workers\Http\Controllers\Concerns\ValidatesLabourScope;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LabourWorkerController extends Controller
{
    use ValidatesLabourScope;

    public function index(): View
    {
        return view('labour::workers.index', [
            'workers' => LabourWorker::query()
                ->with(['organization', 'farm', 'user'])
                ->orderBy('name')
                ->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('labour::workers.form', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        LabourWorker::query()->create($data);

        return redirect()->route('labour.workers.index')->with('status', 'Worker created.');
    }

    public function show(LabourWorker $worker): View
    {
        return view('labour::workers.show', [
            'worker' => $worker->load(['organization', 'farm', 'user', 'teams']),
        ]);
    }

    public function edit(LabourWorker $worker): View
    {
        return view('labour::workers.form', $this->formData($worker));
    }

    public function update(Request $request, LabourWorker $worker): RedirectResponse
    {
        $worker->update($this->validated($request, $worker));

        return redirect()->route('labour.workers.show', $worker)->with('status', 'Worker updated.');
    }

    public function deactivate(LabourWorker $worker): RedirectResponse
    {
        $worker->update(['status' => 'inactive']);

        return redirect()->route('labour.workers.show', $worker)->with('status', 'Worker deactivated.');
    }

    private function formData(?LabourWorker $worker = null): array
    {
        return [
            'worker' => $worker,
            'organizations' => Organization::query()->orderBy('name')->get(),
            'farms' => Farm::query()->with('organization')->orderBy('name')->get(),
            'users' => User::query()->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request, ?LabourWorker $worker = null): array
    {
        $data = $request->validate([
            'organization_id' => ['required', 'integer', Rule::exists('organizations', 'id')],
            'farm_id' => ['required', 'integer', Rule::exists('farms', 'id')],
            'user_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'worker_code' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'employment_type' => ['required', 'string', 'max:255'],
            'primary_role' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'start_date' => ['nullable', 'date'],
            'rate_type' => ['nullable', Rule::in(['hourly', 'daily', 'monthly', 'contract'])],
            'default_rate' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->ensureFarmBelongsToOrganization((int) $data['farm_id'], (int) $data['organization_id']);

        return $data;
    }
}
