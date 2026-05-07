<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_cost_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->nullable()->constrained('farms')->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->string('cost_nature')->default('other');
            $table->string('default_source_module')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'farm_id', 'name']);
            $table->index(['organization_id', 'farm_id', 'is_active']);
        });

        Schema::create('finance_cost_centres', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->nullable()->constrained('farms')->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('centre_type')->default('other');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'farm_id', 'name']);
            $table->index(['organization_id', 'farm_id', 'is_active']);
        });

        Schema::create('finance_cost_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->nullable()->constrained('farms')->cascadeOnDelete();
            $table->foreignId('cost_category_id')->constrained('finance_cost_categories')->restrictOnDelete();
            $table->foreignId('cost_centre_id')->nullable()->constrained('finance_cost_centres')->nullOnDelete();
            $table->string('entry_no');
            $table->date('entry_date');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('source_module')->default('manual');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_label')->nullable();
            $table->decimal('quantity', 14, 4)->nullable();
            $table->string('unit')->nullable();
            $table->decimal('unit_cost', 14, 4)->nullable();
            $table->decimal('amount', 14, 2);
            $table->string('currency', 3)->default('KES');
            $table->string('status')->default('draft');
            $table->string('payment_state')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('voided_at')->nullable();
            $table->text('void_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'entry_no']);
            $table->index(['organization_id', 'farm_id', 'status', 'entry_date']);
            $table->index(['source_module', 'reference_type', 'reference_id']);
        });

        Schema::create('finance_cost_allocations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cost_entry_id')->constrained('finance_cost_entries')->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->nullable()->constrained('farms')->cascadeOnDelete();
            $table->string('allocation_type');
            $table->string('allocatable_type')->nullable();
            $table->unsignedBigInteger('allocatable_id')->nullable();
            $table->string('allocation_label')->nullable();
            $table->decimal('allocation_percent', 7, 4)->nullable();
            $table->decimal('amount', 14, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'farm_id', 'allocation_type']);
            $table->index(['allocatable_type', 'allocatable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_cost_allocations');
        Schema::dropIfExists('finance_cost_entries');
        Schema::dropIfExists('finance_cost_centres');
        Schema::dropIfExists('finance_cost_categories');
    }
};
