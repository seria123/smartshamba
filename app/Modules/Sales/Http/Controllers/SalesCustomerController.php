<?php

namespace App\Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Sales\Models\SalesCustomer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SalesCustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = SalesCustomer::with(['organization', 'farm'])
            ->when($request->input('organization_id'), fn ($query, $id) => $query->where('organization_id', $id))
            ->when($request->input('farm_id'), fn ($query, $id) => $query->where('farm_id', $id))
            ->when($request->input('customer_type'), fn ($query, $type) => $query->where('customer_type', $type))
            ->orderBy('name')->paginate(20);

        return view('sales::customers.index', ['customers' => $customers] + $this->formData());
    }

    public function create(): View { return view('sales::customers.form', $this->formData()); }
    public function show(SalesCustomer $customer): View { return view('sales::customers.show', ['customer' => $customer->load(['organization', 'farm', 'records' => fn ($query) => $query->latest('sale_date')->limit(10)])]); }
    public function edit(SalesCustomer $customer): View { return view('sales::customers.form', $this->formData($customer)); }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $customer = SalesCustomer::query()->create($data + ['created_by' => $request->user()?->id]);
        return redirect()->route('sales.customers.show', $customer)->with('status', 'Customer created.');
    }

    public function update(Request $request, SalesCustomer $customer): RedirectResponse
    {
        $customer->update($this->validated($request) + ['updated_by' => $request->user()?->id]);
        return redirect()->route('sales.customers.show', $customer)->with('status', 'Customer updated.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'farm_id' => ['nullable', 'integer', 'exists:farms,id'],
            'name' => ['required', 'string', 'max:255'],
            'customer_type' => ['nullable', Rule::in(['individual', 'broker', 'retailer', 'processor', 'cooperative', 'institution', 'restaurant', 'exporter', 'other'])],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'id_number' => ['nullable', 'string', 'max:100'],
            'tax_pin' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (! empty($data['farm_id']) && ! Farm::where('id', $data['farm_id'])->where('organization_id', $data['organization_id'])->exists()) {
            throw ValidationException::withMessages(['farm_id' => 'The selected farm must belong to the selected organization.']);
        }

        return $data + ['is_active' => $request->boolean('is_active', true)];
    }

    private function formData(?SalesCustomer $customer = null): array
    {
        return ['customer' => $customer, 'organizations' => Organization::orderBy('name')->get(), 'farms' => Farm::orderBy('name')->get()];
    }
}
