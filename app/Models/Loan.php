<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'loan_name',
        'purpose_tag',
        'lender_name',
        'lender_type',
        'lender_contact',
        'principal_amount',
        'interest_rate',
        'duration_months',
        'interest_method',
        'amount_repaid',
        'loan_date',
        'due_date',
        'repayment_frequency',
        'repayment_schedule',
        'payments',
        'payment_method',
        'remaining_principal',
        'total_interest_paid',
        'crop_cycle_id',
        'livestock_id',
        'allocation_inputs',
        'allocation_labor',
        'allocation_equipment',
        'expected_profit',
        'generated_profit',
        'farm_income_snapshot',
        'risk_level',
        'risk_notes',
        'early_repayment_extra',
        'interest_saved_estimate',
        'comparison_notes',
        'document_paths',
        'late_penalty_rate',
        'extra_charges',
        'group_loan_members',
        'status',
        'notes',
    ];

    protected $casts = [
        'principal_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'duration_months' => 'integer',
        'amount_repaid' => 'decimal:2',
        'repayment_schedule' => 'array',
        'payments' => 'array',
        'remaining_principal' => 'decimal:2',
        'total_interest_paid' => 'decimal:2',
        'allocation_inputs' => 'decimal:2',
        'allocation_labor' => 'decimal:2',
        'allocation_equipment' => 'decimal:2',
        'expected_profit' => 'decimal:2',
        'generated_profit' => 'decimal:2',
        'farm_income_snapshot' => 'decimal:2',
        'early_repayment_extra' => 'decimal:2',
        'interest_saved_estimate' => 'decimal:2',
        'document_paths' => 'array',
        'late_penalty_rate' => 'decimal:2',
        'extra_charges' => 'decimal:2',
        'group_loan_members' => 'array',
        'loan_date' => 'date',
        'due_date' => 'date',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function cropCycle(): BelongsTo
    {
        return $this->belongsTo(CropCycle::class);
    }

    public function livestock(): BelongsTo
    {
        return $this->belongsTo(Livestock::class);
    }

    public function totalPayable(): float
    {
        return (float) $this->principal_amount + $this->totalInterestEstimate() + (float) $this->extra_charges + $this->penaltyEstimate();
    }

    public function balance(): float
    {
        return max(0, $this->totalPayable() - (float) $this->amount_repaid);
    }

    public function isDueSoon(): bool
    {
        return $this->due_date && $this->due_date->between(now(), now()->addDays(3));
    }

    public function totalInterestEstimate(): float
    {
        $principal = (float) $this->principal_amount;
        $rate = (float) $this->interest_rate / 100;
        $months = max(1, (int) ($this->duration_months ?: 12));

        return match ($this->interest_method) {
            'reducing_balance' => $this->reducingBalanceInterest($principal, $rate, $months),
            'compound' => $principal * ((1 + ($rate / 12)) ** $months - 1),
            default => $principal * $rate * ($months / 12),
        };
    }

    public function repaymentSchedule(): array
    {
        return $this->repayment_schedule ?: $this->generateSchedule();
    }

    public function generateSchedule(): array
    {
        $months = max(1, (int) ($this->duration_months ?: 12));
        $principal = (float) $this->principal_amount;
        $monthlyPrincipal = $principal / $months;
        $balance = $principal;
        $schedule = [];

        for ($i = 1; $i <= $months; $i++) {
            $interest = match ($this->interest_method) {
                'reducing_balance' => $balance * (((float) $this->interest_rate / 100) / 12),
                'compound' => $balance * (((float) $this->interest_rate / 100) / 12),
                default => $this->totalInterestEstimate() / $months,
            };

            $principalPart = min($monthlyPrincipal, $balance);
            $balance = max(0, $balance - $principalPart);

            $schedule[] = [
                'installment' => $i,
                'due_date' => $this->loan_date?->copy()->addMonths($i)->toDateString(),
                'principal' => round($principalPart, 2),
                'interest' => round($interest, 2),
                'total' => round($principalPart + $interest, 2),
                'balance' => round($balance, 2),
            ];
        }

        return $schedule;
    }

    public function nextInstallment(): ?array
    {
        return collect($this->repaymentSchedule())
            ->first(fn ($row) => isset($row['due_date']) && now()->lte(\Carbon\Carbon::parse($row['due_date'])));
    }

    public function debtToIncomeRatio(): float
    {
        $income = (float) $this->farm_income_snapshot;

        return $income > 0 ? ($this->balance() / $income) * 100 : 0;
    }

    public function roi(): float
    {
        return (float) $this->principal_amount > 0
            ? (((float) $this->generated_profit - (float) $this->principal_amount) / (float) $this->principal_amount) * 100
            : 0;
    }

    public function penaltyEstimate(): float
    {
        if (! $this->due_date || ! $this->due_date->isPast() || in_array($this->status, ['paid'], true)) {
            return 0;
        }

        return (float) $this->principal_amount * ((float) $this->late_penalty_rate / 100);
    }

    public function earlyRepaymentSavings(): float
    {
        $extra = (float) $this->early_repayment_extra;

        return $extra > 0 ? min($this->totalInterestEstimate(), $extra * ((float) $this->interest_rate / 100)) : 0;
    }

    public function riskSummary(): array
    {
        $ratio = $this->debtToIncomeRatio();

        if ($ratio >= 60 || $this->due_date?->isPast()) {
            return ['level' => 'high', 'notes' => 'High repayment burden or overdue risk. Review cash flow before taking more debt.'];
        }

        if ($ratio >= 35 || (float) $this->interest_rate >= 20) {
            return ['level' => 'medium', 'notes' => 'Moderate repayment pressure. Monitor income and installment timing closely.'];
        }

        return ['level' => 'low', 'notes' => 'Loan burden appears manageable from the recorded income snapshot.'];
    }

    private function reducingBalanceInterest(float $principal, float $rate, int $months): float
    {
        $balance = $principal;
        $monthlyPrincipal = $principal / $months;
        $interest = 0;

        for ($i = 0; $i < $months; $i++) {
            $interest += $balance * ($rate / 12);
            $balance = max(0, $balance - $monthlyPrincipal);
        }

        return $interest;
    }
}
