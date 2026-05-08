<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_customers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->nullable()->constrained('farms')->cascadeOnDelete();
            $table->string('name');
            $table->string('customer_type')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('id_number')->nullable();
            $table->string('tax_pin')->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'farm_id', 'name']);
            $table->index(['organization_id', 'farm_id', 'is_active']);
        });

        Schema::create('sales_catalog_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->nullable()->constrained('farms')->cascadeOnDelete();
            $table->string('name');
            $table->string('category');
            $table->string('unit');
            $table->string('sku')->nullable();
            $table->decimal('default_unit_price', 14, 2)->nullable();
            $table->string('currency', 3)->default('KES');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'farm_id', 'name']);
            $table->index(['organization_id', 'farm_id', 'category', 'is_active']);
        });

        Schema::create('sales_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('sales_customer_id')->nullable()->constrained('sales_customers')->nullOnDelete();
            $table->string('sale_number');
            $table->date('sale_date');
            $table->string('status')->default('draft');
            $table->string('payment_status')->default('unpaid');
            $table->string('channel')->nullable();
            $table->string('currency', 3)->default('KES');
            $table->decimal('subtotal_amount', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('other_charges_amount', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->decimal('amount_paid', 14, 2)->default(0);
            $table->decimal('balance_amount', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('voided_at')->nullable();
            $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('void_reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'sale_number']);
            $table->index(['organization_id', 'farm_id', 'status', 'sale_date']);
            $table->index(['sales_customer_id', 'payment_status']);
        });

        Schema::create('sales_record_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sales_record_id')->constrained('sales_records')->cascadeOnDelete();
            $table->foreignId('sales_catalog_item_id')->nullable()->constrained('sales_catalog_items')->nullOnDelete();
            $table->string('description');
            $table->string('category')->nullable();
            $table->string('unit');
            $table->decimal('quantity', 14, 3);
            $table->decimal('unit_price', 14, 2);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('line_total_amount', 14, 2)->default(0);
            $table->foreignId('crop_cycle_id')->nullable()->constrained('crop_cycles')->nullOnDelete();
            $table->foreignId('crop_harvest_id')->nullable()->constrained('crop_harvest_records')->nullOnDelete();
            $table->foreignId('animal_id')->nullable()->constrained('livestock_animals')->nullOnDelete();
            $table->foreignId('animal_group_id')->nullable()->constrained('livestock_animal_groups')->nullOnDelete();
            $table->foreignId('livestock_yield_id')->nullable()->constrained('livestock_yield_records')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['category', 'crop_cycle_id']);
            $table->index(['animal_id', 'animal_group_id', 'livestock_yield_id']);
        });

        Schema::create('sales_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sales_record_id')->constrained('sales_records')->cascadeOnDelete();
            $table->date('payment_date');
            $table->decimal('amount', 14, 2);
            $table->string('method');
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['sales_record_id', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_payments');
        Schema::dropIfExists('sales_record_lines');
        Schema::dropIfExists('sales_records');
        Schema::dropIfExists('sales_catalog_items');
        Schema::dropIfExists('sales_customers');
    }
};
