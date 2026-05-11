<?php

namespace App\Modules\Audit\Http\Controllers\Concerns;

use App\Modules\Audit\Services\AuditAccessContext;
use Illuminate\Http\Request;

trait PreparesAuditRequests
{
    protected function prepare(Request $request): array
    {
        $context = AuditAccessContext::forUser($request->user());
        $filters = $context->filters($request->query());

        return [
            'context' => $context,
            'filters' => $filters,
            'organizations' => $context->organizations(),
            'farms' => $context->farms(isset($filters['organization_id']) ? (int) $filters['organization_id'] : null),
        ];
    }
}
