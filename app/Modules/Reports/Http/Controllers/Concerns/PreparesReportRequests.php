<?php

namespace App\Modules\Reports\Http\Controllers\Concerns;

use App\Modules\Reports\Services\ReportsAccessContext;
use Illuminate\Http\Request;

trait PreparesReportRequests
{
    private function prepareReport(Request $request): array
    {
        $validated = $request->validate([
            'organization_id' => ['nullable', 'integer'],
            'farm_id' => ['nullable', 'integer'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'crop_cycle_id' => ['nullable', 'integer'],
            'customer_id' => ['nullable', 'integer'],
            'cost_category_id' => ['nullable', 'integer'],
            'target_type' => ['nullable', 'string', 'max:80'],
            'status' => ['nullable', 'string', 'max:80'],
        ]);

        $context = ReportsAccessContext::forUser($request->user());
        $filters = $context->filters($validated);

        return [
            'filters' => $filters,
            'organizations' => $context->organizations(),
            'farms' => $context->farms($filters['organization_id'] ?? null),
        ];
    }
}
