<?php

namespace App\Modules\Tasks\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Paddock;
use App\Modules\Core\Models\Site;
use App\Modules\Core\Models\Warehouse;
use App\Modules\Tasks\Http\Controllers\Concerns\ValidatesTaskScope;
use App\Modules\Tasks\Models\OpsWorkOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WorkOrderController extends Controller
{
    use ValidatesTaskScope;

    public function index(): View
    {
        return view('tasks::work-orders.index', [
            'workOrders' => OpsWorkOrder::query()
                ->with(['farm', 'tasks'])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('tasks::work-orders.form', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['work_order_number'] = $this->nextNumber('WO', 'ops_work_orders', 'work_order_number');
        $data['created_by'] = $request->user()->id;

        $workOrder = OpsWorkOrder::query()->create($data);

        return redirect()->route('tasks.work-orders.show', $workOrder)->with('status', 'Work order created.');
    }

    public function show(OpsWorkOrder $workOrder): View
    {
        return view('tasks::work-orders.show', [
            'workOrder' => $workOrder->load(['organization', 'farm', 'site', 'field', 'paddock', 'warehouse', 'tasks']),
        ]);
    }

    public function edit(OpsWorkOrder $workOrder): View
    {
        return view('tasks::work-orders.form', $this->formData($workOrder));
    }

    public function update(Request $request, OpsWorkOrder $workOrder): RedirectResponse
    {
        $workOrder->update($this->validated($request));

        return redirect()->route('tasks.work-orders.show', $workOrder)->with('status', 'Work order updated.');
    }

    public function cancel(Request $request, OpsWorkOrder $workOrder): RedirectResponse
    {
        $data = $request->validate([
            'cancellation_reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $workOrder->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $data['cancellation_reason'] ?? null,
        ]);

        return redirect()->route('tasks.work-orders.show', $workOrder)->with('status', 'Work order cancelled.');
    }

    private function formData(?OpsWorkOrder $workOrder = null): array
    {
        return [
            'workOrder' => $workOrder,
            'organizations' => Organization::query()->orderBy('name')->get(),
            'farms' => Farm::query()->with('organization')->orderBy('name')->get(),
            'sites' => Site::query()->with('farm')->orderBy('name')->get(),
            'fields' => Field::query()->with('farm')->orderBy('name')->get(),
            'paddocks' => Paddock::query()->with('farm')->orderBy('name')->get(),
            'warehouses' => Warehouse::query()->with('farm')->orderBy('name')->get(),
            'users' => User::query()->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'organization_id' => ['required', 'integer', Rule::exists('organizations', 'id')],
            'farm_id' => ['required', 'integer', Rule::exists('farms', 'id')],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => ['required', Rule::in(['general', 'crop', 'livestock', 'irrigation', 'inventory', 'maintenance', 'harvest', 'feeding', 'treatment', 'cleaning', 'security', 'other'])],
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'status' => ['required', Rule::in(['draft', 'planned', 'assigned', 'in_progress', 'submitted', 'approved', 'completed', 'cancelled', 'rejected', 'needs_correction'])],
            'site_id' => ['nullable', 'integer', Rule::exists('sites', 'id')],
            'field_id' => ['nullable', 'integer', Rule::exists('fields', 'id')],
            'paddock_id' => ['nullable', 'integer', Rule::exists('paddocks', 'id')],
            'warehouse_id' => ['nullable', 'integer', Rule::exists('warehouses', 'id')],
            'requested_by' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->ensureFarmBelongsToOrganization((int) $data['farm_id'], (int) $data['organization_id']);
        $this->ensureLocationsBelongToFarm($data);

        if ($data['requested_by'] ?? null) {
            $this->ensureUserHasOrganizationAccess((int) $data['requested_by'], (int) $data['organization_id']);
        }

        return $data;
    }
}
