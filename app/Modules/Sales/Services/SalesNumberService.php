<?php

namespace App\Modules\Sales\Services;

use App\Modules\Sales\Models\SalesRecord;

class SalesNumberService
{
    public function next(int $organizationId): string
    {
        $year = now()->format('Y');
        $count = SalesRecord::withTrashed()
            ->where('organization_id', $organizationId)
            ->where('sale_number', 'like', 'SALE-'.$year.'-%')
            ->count() + 1;

        do {
            $number = 'SALE-'.$year.'-'.str_pad((string) $count, 6, '0', STR_PAD_LEFT);
            $count++;
        } while (SalesRecord::withTrashed()->where('organization_id', $organizationId)->where('sale_number', $number)->exists());

        return $number;
    }
}
