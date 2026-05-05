<?php

namespace App\Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Warehouse;
use App\Modules\Inventory\Http\Controllers\Concerns\PostsInventoryMovements;
use App\Modules\Inventory\Models\InventoryMovement;
use App\Modules\Inventory\Models\InventoryProduct;
use App\Modules\Inventory\Models\InventoryStockLot;
use App\Modules\Inventory\Models\InventorySupplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StockController extends Controller
{
    use PostsInventoryMovements;

    public function balances(): View
    {
        return view('inventory::stock.balances', ['lots' => InventoryStockLot::with(['organization', 'farm', 'warehouse', 'product', 'supplier'])->orderByDesc('id')->paginate(20)]);
    }

    public function movements(): View
    {
        return view('inventory::movements.index', ['movements' => InventoryMovement::with(['product', 'warehouse', 'fromWarehouse', 'toWarehouse', 'stockLot'])->latest()->paginate(20)]);
    }

    public function receiveForm(): View { return view('inventory::stock.receive', $this->formData()); }
    public function issueForm(): View { return view('inventory::stock.issue', $this->formData()); }
    public function transferForm(): View { return view('inventory::stock.transfer', $this->formData()); }
    public function adjustmentForm(): View { return view('inventory::stock.adjust', $this->formData()); }

    public function receive(Request $request): RedirectResponse
    {
        $data = $this->validatedIncoming($request);
        $product = $this->ensureProductBelongsToOrganization((int) $data['product_id'], (int) $data['organization_id']);
        $this->ensureSupplierBelongsToOrganization($data['supplier_id'] ?? null, (int) $data['organization_id']);
        $this->ensureWarehouseBelongsToFarm((int) $data['warehouse_id'], (int) $data['farm_id']);

        $lot = InventoryStockLot::query()->create([
            'organization_id' => $data['organization_id'],
            'farm_id' => $data['farm_id'],
            'warehouse_id' => $data['warehouse_id'],
            'product_id' => $data['product_id'],
            'supplier_id' => $data['supplier_id'] ?? null,
            'lot_number' => $data['lot_number'],
            'batch_number' => $data['batch_number'] ?? null,
            'expiry_date' => $data['expiry_date'] ?? null,
            'quantity_on_hand' => 0,
            'reserved_quantity' => 0,
            'unit_of_measure' => $data['unit_of_measure'] ?: $product->unit_of_measure,
            'unit_cost' => $data['unit_cost'],
            'currency' => $data['currency'] ?? 'KES',
            'status' => 'active',
        ]);
        $this->postIncoming($lot, $data['movement_type'], (float) $data['quantity'], (float) $data['unit_cost'], $data['supplier_id'] ?? null, $data['reason'] ?? null, $request->user()->id);

        return redirect()->route('inventory.stock.balances')->with('status', 'Stock received.');
    }

    public function issue(Request $request): RedirectResponse
    {
        $data = $this->validatedOutgoing($request, ['issue']);
        $lot = $this->ensureLotBelongsToFarm((int) $data['stock_lot_id'], (int) $data['farm_id']);
        $this->postOutgoing($lot, 'issue', (float) $data['quantity'], $data['reason'] ?? null, $request->user()->id);
        return redirect()->route('inventory.stock.balances')->with('status', 'Stock issued.');
    }

    public function transfer(Request $request): RedirectResponse
    {
        $data = $this->validatedOutgoing($request, ['transfer']);
        $lot = $this->ensureLotBelongsToFarm((int) $data['stock_lot_id'], (int) $data['farm_id']);
        $this->postTransfer($lot, (int) $data['to_warehouse_id'], (float) $data['quantity'], $data['reason'] ?? null, $request->user()->id);
        return redirect()->route('inventory.stock.balances')->with('status', 'Stock transferred.');
    }

    public function adjust(Request $request): RedirectResponse
    {
        $data = $this->validatedOutgoing($request, ['adjustment_in', 'adjustment_out', 'damage', 'loss', 'expiry_disposal', 'correction']);
        $lot = $this->ensureLotBelongsToFarm((int) $data['stock_lot_id'], (int) $data['farm_id']);
        if (in_array($data['movement_type'], ['adjustment_in', 'correction'], true)) {
            $this->postIncoming($lot, $data['movement_type'], (float) $data['quantity'], (float) $lot->unit_cost, $lot->supplier_id, $data['reason'] ?? null, $request->user()->id);
        } else {
            $this->postOutgoing($lot, $data['movement_type'], (float) $data['quantity'], $data['reason'] ?? null, $request->user()->id);
        }
        return redirect()->route('inventory.stock.balances')->with('status', 'Stock adjusted.');
    }

    private function formData(): array
    {
        return [
            'organizations' => Organization::query()->orderBy('name')->get(),
            'farms' => Farm::query()->with('organization')->orderBy('name')->get(),
            'warehouses' => Warehouse::query()->with('farm')->orderBy('name')->get(),
            'products' => InventoryProduct::query()->orderBy('name')->get(),
            'suppliers' => InventorySupplier::query()->orderBy('name')->get(),
            'lots' => InventoryStockLot::query()->with(['product', 'warehouse'])->orderByDesc('id')->get(),
        ];
    }

    private function validatedIncoming(Request $request): array
    {
        $data = $request->validate([
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'farm_id' => ['required', 'integer', 'exists:farms,id'],
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'product_id' => ['required', 'integer', 'exists:inventory_products,id'],
            'supplier_id' => ['nullable', 'integer', 'exists:inventory_suppliers,id'],
            'lot_number' => ['required', 'string', 'max:255'],
            'batch_number' => ['nullable', 'string', 'max:255'],
            'expiry_date' => ['nullable', 'date'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'unit_of_measure' => ['nullable', 'string', 'max:255'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'movement_type' => ['required', Rule::in(['opening_balance', 'purchase_receipt', 'adjustment_in'])],
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);
        $this->ensureFarmBelongsToOrganization((int) $data['farm_id'], (int) $data['organization_id']);
        return $data;
    }

    private function validatedOutgoing(Request $request, array $types): array
    {
        $data = $request->validate([
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'farm_id' => ['required', 'integer', 'exists:farms,id'],
            'stock_lot_id' => ['required', 'integer', 'exists:inventory_stock_lots,id'],
            'to_warehouse_id' => ['nullable', 'integer', 'exists:warehouses,id'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'movement_type' => ['required', Rule::in($types)],
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);
        $this->ensureFarmBelongsToOrganization((int) $data['farm_id'], (int) $data['organization_id']);
        return $data;
    }
}
