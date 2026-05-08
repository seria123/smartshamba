<?php

namespace App\Modules\Sales\Services;

use App\Modules\Sales\Models\SalesPayment;
use App\Modules\Sales\Models\SalesRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalesPaymentService
{
    public function __construct(private SalesRecordService $records) {}

    public function record(SalesRecord $sale, array $data, ?int $userId = null): SalesPayment
    {
        if ($sale->status !== 'confirmed') {
            throw ValidationException::withMessages(['sales_record_id' => 'Payments can only be recorded against confirmed sales.']);
        }

        $amount = (float) $data['amount'];
        if ($amount <= 0) {
            throw ValidationException::withMessages(['amount' => 'Payment amount must be greater than zero.']);
        }
        if (round((float) $sale->amount_paid + $amount, 2) - (float) $sale->total_amount > 0.01) {
            throw ValidationException::withMessages(['amount' => 'Payment amount cannot exceed the outstanding sale balance.']);
        }

        return DB::transaction(function () use ($sale, $data, $userId): SalesPayment {
            $payment = $sale->payments()->create(collect($data)->only(['payment_date', 'amount', 'method', 'reference', 'notes'])->all() + ['created_by' => $userId]);
            $this->records->recalculate($sale);

            return $payment;
        });
    }
}
