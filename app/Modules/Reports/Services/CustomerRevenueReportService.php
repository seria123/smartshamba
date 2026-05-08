<?php

namespace App\Modules\Reports\Services;

use App\Modules\Sales\Models\SalesRecord;
use App\Modules\Sales\Models\SalesRecordLine;
use Illuminate\Support\Collection;

class CustomerRevenueReportService
{
    public function rows(array $filters): Collection
    {
        return SalesRecord::query()
            ->leftJoin('sales_customers', 'sales_customers.id', '=', 'sales_records.sales_customer_id')
            ->where('sales_records.status', 'confirmed')
            ->when($filters['organization_id'] ?? null, fn ($query, $id) => $query->where('sales_records.organization_id', $id))
            ->when($filters['farm_id'] ?? null, fn ($query, $id) => $query->where('sales_records.farm_id', $id))
            ->when($filters['customer_id'] ?? null, fn ($query, $id) => $query->where('sales_records.sales_customer_id', $id))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('sales_records.sale_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('sales_records.sale_date', '<=', $date))
            ->selectRaw("sales_records.sales_customer_id, coalesce(sales_customers.name, 'Walk-in / Unspecified') as customer, count(*) as sales_count, sum(total_amount) as total_sales, sum(amount_paid) as amount_paid, sum(balance_amount) as outstanding_amount, max(sale_date) as last_sale_date")
            ->groupBy('sales_records.sales_customer_id', 'sales_customers.name')
            ->orderByDesc('total_sales')
            ->get()
            ->map(function ($row): array {
                return [
                    'customer' => $row->customer,
                    'sales_count' => $row->sales_count,
                    'total_sales' => (float) $row->total_sales,
                    'amount_paid' => (float) $row->amount_paid,
                    'outstanding_amount' => (float) $row->outstanding_amount,
                    'last_sale_date' => $row->last_sale_date,
                    'top_item' => $this->topItem($row->sales_customer_id),
                ];
            });
    }

    private function topItem(mixed $customerId): string
    {
        $row = SalesRecordLine::query()
            ->join('sales_records', 'sales_records.id', '=', 'sales_record_lines.sales_record_id')
            ->where('sales_records.status', 'confirmed')
            ->when($customerId, fn ($query, $id) => $query->where('sales_records.sales_customer_id', $id), fn ($query) => $query->whereNull('sales_records.sales_customer_id'))
            ->selectRaw("coalesce(sales_record_lines.description, sales_record_lines.category, 'Unspecified') as label, sum(sales_record_lines.line_total_amount) as total")
            ->groupBy('sales_record_lines.description', 'sales_record_lines.category')
            ->orderByDesc('total')
            ->first();

        return $row?->label ?? '—';
    }
}
