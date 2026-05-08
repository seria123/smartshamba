<?php

namespace Database\Seeders;

use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Sales\Models\SalesCatalogItem;
use App\Modules\Sales\Models\SalesCustomer;
use App\Modules\Sales\Services\SalesPaymentService;
use App\Modules\Sales\Services\SalesRecordService;
use Illuminate\Database\Seeder;

class SalesRevenueSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $organization = Organization::query()->first();
        $farm = Farm::query()->where('organization_id', $organization?->id)->first();
        if (! $organization || ! $farm) {
            return;
        }

        $customer = SalesCustomer::query()->firstOrCreate(
            ['organization_id' => $organization->id, 'farm_id' => $farm->id, 'name' => 'Demo Produce Buyer'],
            ['customer_type' => 'retailer', 'phone' => '+254700000000', 'is_active' => true],
        );

        $maize = SalesCatalogItem::query()->firstOrCreate(
            ['organization_id' => $organization->id, 'farm_id' => $farm->id, 'name' => 'Maize Grain'],
            ['category' => 'crop_produce', 'unit' => 'kg', 'default_unit_price' => 55, 'currency' => 'KES', 'is_active' => true],
        );

        $milk = SalesCatalogItem::query()->firstOrCreate(
            ['organization_id' => $organization->id, 'farm_id' => $farm->id, 'name' => 'Fresh Milk'],
            ['category' => 'animal_product', 'unit' => 'litre', 'default_unit_price' => 70, 'currency' => 'KES', 'is_active' => true],
        );

        if (! \App\Modules\Sales\Models\SalesRecord::query()->where('organization_id', $organization->id)->where('notes', 'Demo sales revenue seed')->exists()) {
            $service = app(SalesRecordService::class);
            $paymentService = app(SalesPaymentService::class);
            $sale = $service->create([
                'organization_id' => $organization->id,
                'farm_id' => $farm->id,
                'sales_customer_id' => $customer->id,
                'sale_date' => now()->toDateString(),
                'channel' => 'farm_gate',
                'currency' => 'KES',
                'notes' => 'Demo sales revenue seed',
                'lines' => [
                    ['sales_catalog_item_id' => $maize->id, 'description' => 'Maize Grain', 'category' => 'crop_produce', 'unit' => 'kg', 'quantity' => 120, 'unit_price' => 55],
                    ['sales_catalog_item_id' => $milk->id, 'description' => 'Fresh Milk', 'category' => 'animal_product', 'unit' => 'litre', 'quantity' => 40, 'unit_price' => 70],
                ],
            ]);
            $service->confirm($sale, null);
            $paymentService->record($sale->fresh(), ['payment_date' => now()->toDateString(), 'amount' => 5000, 'method' => 'cash'], null);
        }
    }
}
