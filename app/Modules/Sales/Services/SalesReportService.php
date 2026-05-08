<?php

namespace App\Modules\Sales\Services;

use App\Modules\Finance\Models\FinanceCostAllocation;
use App\Modules\Sales\Models\SalesRecord;
use App\Modules\Sales\Models\SalesRecordLine;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SalesReportService
{
    public function dashboard(array $filters = []): array
    {
        $month = now()->format('Y-m');
        $monthBase = $this->filteredRecords($filters)->where('status', 'confirmed')->whereRaw("substr(sale_date, 1, 7) = ?", [$month]);
        $base = $this->filteredRecords($filters)->where('status', 'confirmed');

        return [
            'monthTotal' => (clone $monthBase)->sum('total_amount'),
            'monthPaid' => (clone $monthBase)->sum('amount_paid'),
            'outstanding' => (clone $base)->sum('balance_amount'),
            'recentSales' => $this->filteredRecords($filters)->with(['customer', 'farm'])->latest('sale_date')->limit(10)->get(),
            'topCustomers' => $this->byCustomer($filters)->take(5),
            'topItems' => $this->byItem($filters)->take(5),
        ];
    }

    public function summary(array $filters = []): array
    {
        $base = $this->filteredRecords($filters);

        return [
            'confirmedTotal' => (clone $base)->where('status', 'confirmed')->sum('total_amount'),
            'paidTotal' => (clone $base)->where('status', 'confirmed')->sum('amount_paid'),
            'balanceTotal' => (clone $base)->where('status', 'confirmed')->sum('balance_amount'),
            'draftTotal' => (clone $base)->where('status', 'draft')->sum('total_amount'),
            'voidTotal' => (clone $base)->where('status', 'void')->sum('total_amount'),
            'byFarm' => $this->byFarm($filters),
            'byCategory' => $this->byCategory($filters),
        ];
    }

    public function byCustomer(array $filters = []): Collection
    {
        return $this->filteredRecords($filters)->where('sales_records.status', 'confirmed')
            ->leftJoin('sales_customers', 'sales_customers.id', '=', 'sales_records.sales_customer_id')
            ->selectRaw("coalesce(sales_customers.name, 'Walk-in / Unspecified') as label, sum(sales_records.total_amount) as total, sum(sales_records.balance_amount) as balance")
            ->groupBy('sales_customers.name')->orderByDesc('total')->get();
    }

    public function byItem(array $filters = []): Collection
    {
        return $this->filteredLines($filters)
            ->leftJoin('sales_catalog_items', 'sales_catalog_items.id', '=', 'sales_record_lines.sales_catalog_item_id')
            ->selectRaw("coalesce(sales_catalog_items.name, sales_record_lines.description) as label, sales_record_lines.category as category, sum(sales_record_lines.quantity) as quantity, sum(sales_record_lines.line_total_amount) as total")
            ->groupBy('sales_catalog_items.name', 'sales_record_lines.description', 'sales_record_lines.category')->orderByDesc('total')->get();
    }

    public function byCategory(array $filters = []): Collection
    {
        return $this->filteredLines($filters)
            ->selectRaw("coalesce(sales_record_lines.category, 'uncategorized') as label, sum(sales_record_lines.line_total_amount) as total")
            ->groupBy('sales_record_lines.category')->orderByDesc('total')->get();
    }

    public function byFarm(array $filters = []): Collection
    {
        return $this->filteredRecords($filters)->where('sales_records.status', 'confirmed')
            ->join('farms', 'farms.id', '=', 'sales_records.farm_id')
            ->selectRaw('farms.name as label, sum(sales_records.total_amount) as total, sum(sales_records.balance_amount) as balance')
            ->groupBy('farms.name')->orderByDesc('total')->get();
    }

    public function byCropCycle(array $filters = []): Collection
    {
        return $this->filteredLines($filters)->whereNotNull('sales_record_lines.crop_cycle_id')
            ->leftJoin('crop_cycles', 'crop_cycles.id', '=', 'sales_record_lines.crop_cycle_id')
            ->selectRaw("coalesce(crop_cycles.name, sales_record_lines.crop_cycle_id) as label, sum(sales_record_lines.line_total_amount) as total")
            ->groupBy('crop_cycles.name', 'sales_record_lines.crop_cycle_id')->orderByDesc('total')->get();
    }

    public function byLivestock(array $filters = []): Collection
    {
        return $this->filteredLines($filters)->where(fn ($query) => $query->whereNotNull('sales_record_lines.animal_id')->orWhereNotNull('sales_record_lines.animal_group_id')->orWhereNotNull('sales_record_lines.livestock_yield_id'))
            ->leftJoin('livestock_animals', 'livestock_animals.id', '=', 'sales_record_lines.animal_id')
            ->leftJoin('livestock_animal_groups', 'livestock_animal_groups.id', '=', 'sales_record_lines.animal_group_id')
            ->selectRaw("coalesce(livestock_animals.name, livestock_animals.animal_code, livestock_animal_groups.name, sales_record_lines.description) as label, sum(sales_record_lines.line_total_amount) as total")
            ->groupBy('livestock_animals.name', 'livestock_animals.animal_code', 'livestock_animal_groups.name', 'sales_record_lines.description')->orderByDesc('total')->get();
    }

    public function grossMargin(array $filters = []): Collection
    {
        $revenue = $this->filteredLines($filters)
            ->where(fn ($query) => $query->whereNotNull('sales_record_lines.crop_cycle_id')->orWhereNotNull('sales_record_lines.animal_id')->orWhereNotNull('sales_record_lines.animal_group_id'))
            ->get(['sales_record_lines.crop_cycle_id', 'sales_record_lines.animal_id', 'sales_record_lines.animal_group_id', 'sales_record_lines.line_total_amount']);

        $rows = collect();
        foreach ([['crop_cycle_id', 'crop_cycle'], ['animal_id', 'livestock_animal'], ['animal_group_id', 'livestock_group']] as [$field, $type]) {
            $ids = $revenue->pluck($field)->filter()->unique()->values();
            foreach ($ids as $id) {
                $amount = (float) $revenue->where($field, $id)->sum('line_total_amount');
                $cost = (float) FinanceCostAllocation::query()
                    ->join('finance_cost_entries', 'finance_cost_entries.id', '=', 'finance_cost_allocations.cost_entry_id')
                    ->where('finance_cost_entries.status', 'confirmed')
                    ->where('finance_cost_allocations.allocation_type', $type)
                    ->where('finance_cost_allocations.allocatable_id', $id)
                    ->sum('finance_cost_allocations.amount');
                $rows->push(['label' => $type.' #'.$id, 'target_type' => $type, 'revenue' => $amount, 'cost' => $cost, 'margin' => $amount - $cost, 'margin_percent' => $amount > 0 ? (($amount - $cost) / $amount) * 100 : 0]);
            }
        }

        return $rows->sortByDesc('revenue')->values();
    }

    public function filteredRecords(array $filters = []): Builder
    {
        return SalesRecord::query()
            ->when($filters['organization_id'] ?? null, fn ($query, $id) => $query->where('organization_id', $id))
            ->when($filters['farm_id'] ?? null, fn ($query, $id) => $query->where('farm_id', $id))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('sale_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('sale_date', '<=', $date))
            ->when($filters['customer_id'] ?? null, fn ($query, $id) => $query->where('sales_customer_id', $id))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['payment_status'] ?? null, fn ($query, $status) => $query->where('payment_status', $status));
    }

    private function filteredLines(array $filters = []): Builder
    {
        return SalesRecordLine::query()
            ->join('sales_records', 'sales_records.id', '=', 'sales_record_lines.sales_record_id')
            ->where('sales_records.status', 'confirmed')
            ->when($filters['organization_id'] ?? null, fn ($query, $id) => $query->where('sales_records.organization_id', $id))
            ->when($filters['farm_id'] ?? null, fn ($query, $id) => $query->where('sales_records.farm_id', $id))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('sales_records.sale_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('sales_records.sale_date', '<=', $date))
            ->when($filters['customer_id'] ?? null, fn ($query, $id) => $query->where('sales_records.sales_customer_id', $id))
            ->when($filters['category'] ?? null, fn ($query, $category) => $query->where('sales_record_lines.category', $category))
            ->when($filters['crop_cycle_id'] ?? null, fn ($query, $id) => $query->where('sales_record_lines.crop_cycle_id', $id))
            ->when($filters['animal_id'] ?? null, fn ($query, $id) => $query->where('sales_record_lines.animal_id', $id))
            ->when($filters['animal_group_id'] ?? null, fn ($query, $id) => $query->where('sales_record_lines.animal_group_id', $id));
    }
}
