<?php

namespace App\Modules\Sales\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesRecord extends Model
{
    use SoftDeletes;

    protected $fillable = ['organization_id', 'farm_id', 'sales_customer_id', 'sale_number', 'sale_date', 'status', 'payment_status', 'channel', 'currency', 'subtotal_amount', 'discount_amount', 'other_charges_amount', 'total_amount', 'amount_paid', 'balance_amount', 'notes', 'confirmed_at', 'confirmed_by', 'voided_at', 'voided_by', 'void_reason', 'created_by', 'updated_by'];

    protected function casts(): array
    {
        return ['sale_date' => 'date', 'subtotal_amount' => 'decimal:2', 'discount_amount' => 'decimal:2', 'other_charges_amount' => 'decimal:2', 'total_amount' => 'decimal:2', 'amount_paid' => 'decimal:2', 'balance_amount' => 'decimal:2', 'confirmed_at' => 'datetime', 'voided_at' => 'datetime'];
    }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function customer(): BelongsTo { return $this->belongsTo(SalesCustomer::class, 'sales_customer_id'); }
    public function lines(): HasMany { return $this->hasMany(SalesRecordLine::class, 'sales_record_id'); }
    public function payments(): HasMany { return $this->hasMany(SalesPayment::class, 'sales_record_id'); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function confirmedBy(): BelongsTo { return $this->belongsTo(User::class, 'confirmed_by'); }
    public function voidedBy(): BelongsTo { return $this->belongsTo(User::class, 'voided_by'); }
}
