<?php

namespace App\Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Organization;
use App\Modules\Inventory\Models\InventorySupplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View { return view('inventory::suppliers.index', ['suppliers' => InventorySupplier::with('organization')->orderBy('name')->paginate(20)]); }
    public function create(): View { return view('inventory::suppliers.form', $this->formData()); }
    public function show(InventorySupplier $supplier): View { return view('inventory::suppliers.show', ['supplier' => $supplier->load('organization')]); }
    public function edit(InventorySupplier $supplier): View { return view('inventory::suppliers.form', $this->formData($supplier)); }

    public function store(Request $request): RedirectResponse
    {
        InventorySupplier::query()->create($this->validated($request));
        return redirect()->route('inventory.suppliers.index')->with('status', 'Supplier created.');
    }

    public function update(Request $request, InventorySupplier $supplier): RedirectResponse
    {
        $supplier->update($this->validated($request));
        return redirect()->route('inventory.suppliers.show', $supplier)->with('status', 'Supplier updated.');
    }

    public function deactivate(InventorySupplier $supplier): RedirectResponse
    {
        $supplier->update(['status' => 'inactive']);
        return redirect()->route('inventory.suppliers.show', $supplier)->with('status', 'Supplier deactivated.');
    }

    private function formData(?InventorySupplier $supplier = null): array
    {
        return ['supplier' => $supplier, 'organizations' => Organization::query()->orderBy('name')->get()];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'supplier_type' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
