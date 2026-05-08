<?php

namespace App\Modules\Reports\Services;

use App\Modules\Finance\Models\FinanceCostEntry;
use App\Modules\Reports\Services\Concerns\BuildsReportQueries;
use App\Modules\Sales\Models\SalesPayment;
use App\Modules\Sales\Models\SalesRecord;
use Illuminate\Support\Collection;

class MonthlyTrendsReportService
{
    use BuildsReportQueries;

    public function rows(array $filters): Collection
    {
        $rows = collect();
        foreach ($this->costs($filters) as $row) {
            $rows[$row->month] = ['month' => $row->month, 'costs' => (float) $row->total, 'revenue' => 0.0, 'payments' => 0.0, 'outstanding' => 0.0];
        }
        foreach ($this->revenue($filters) as $row) {
            $current = $rows[$row->month] ?? ['month' => $row->month, 'costs' => 0.0, 'revenue' => 0.0, 'payments' => 0.0, 'outstanding' => 0.0];
            $current['revenue'] = (float) $row->total;
            $current['outstanding'] = (float) $row->outstanding;
            $rows[$row->month] = $current;
        }
        foreach ($this->payments($filters) as $row) {
            $current = $rows[$row->month] ?? ['month' => $row->month, 'costs' => 0.0, 'revenue' => 0.0, 'payments' => 0.0, 'outstanding' => 0.0];
            $current['payments'] = (float) $row->total;
            $rows[$row->month] = $current;
        }

        return $rows->values()->map(function (array $row): array {
            $row['margin'] = $row['revenue'] - $row['costs'];

            return $row;
        })->sortBy('month')->values();
    }

    private function costs(array $filters): Collection
    {
        return FinanceCostEntry::query()
            ->where('status', 'confirmed')
            ->when($filters['organization_id'] ?? null, fn ($query, $id) => $query->where('organization_id', $id))
            ->when($filters['farm_id'] ?? null, fn ($query, $id) => $query->where('farm_id', $id))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('entry_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('entry_date', '<=', $date))
            ->selectRaw('substr(entry_date, 1, 7) as month, sum(amount) as total')
            ->groupByRaw('substr(entry_date, 1, 7)')
            ->get();
    }

    private function revenue(array $filters): Collection
    {
        return SalesRecord::query()
            ->where('status', 'confirmed')
            ->when($filters['organization_id'] ?? null, fn ($query, $id) => $query->where('organization_id', $id))
            ->when($filters['farm_id'] ?? null, fn ($query, $id) => $query->where('farm_id', $id))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('sale_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('sale_date', '<=', $date))
            ->selectRaw('substr(sale_date, 1, 7) as month, sum(total_amount) as total, sum(balance_amount) as outstanding')
            ->groupByRaw('substr(sale_date, 1, 7)')
            ->get();
    }

    private function payments(array $filters): Collection
    {
        return SalesPayment::query()
            ->join('sales_records', 'sales_records.id', '=', 'sales_payments.sales_record_id')
            ->where('sales_records.status', 'confirmed')
            ->when($filters['organization_id'] ?? null, fn ($query, $id) => $query->where('sales_records.organization_id', $id))
            ->when($filters['farm_id'] ?? null, fn ($query, $id) => $query->where('sales_records.farm_id', $id))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('sales_payments.payment_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('sales_payments.payment_date', '<=', $date))
            ->selectRaw('substr(sales_payments.payment_date, 1, 7) as month, sum(sales_payments.amount) as total')
            ->groupByRaw('substr(sales_payments.payment_date, 1, 7)')
            ->get();
    }
}
