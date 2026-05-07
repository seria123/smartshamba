<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('asset_categories')->nullOnDelete();
            $table->boolean('is_system')->default(false);
            $table->string('status')->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'slug']);
            $table->index(['organization_id', 'status']);
        });

        Schema::create('assets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('asset_categories')->restrictOnDelete();
            $table->string('asset_code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('asset_type');
            $table->string('serial_number')->nullable();
            $table->string('model')->nullable();
            $table->string('manufacturer')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 14, 2)->nullable();
            $table->string('currency')->nullable();
            $table->decimal('current_value', 14, 2)->nullable();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->foreignId('field_id')->nullable()->constrained('fields')->nullOnDelete();
            $table->foreignId('paddock_id')->nullable()->constrained('paddocks')->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->foreignId('assigned_worker_id')->nullable()->constrained('labour_workers')->nullOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('active');
            $table->string('condition_status')->default('unknown');
            $table->string('acquisition_source')->nullable();
            $table->date('warranty_expiry_date')->nullable();
            $table->date('last_service_date')->nullable();
            $table->date('next_service_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['farm_id', 'asset_code']);
            $table->index(['organization_id', 'farm_id', 'status']);
            $table->index(['next_service_date', 'status']);
        });

        Schema::create('asset_maintenance_schedules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained('assets')->restrictOnDelete();
            $table->string('schedule_number')->unique();
            $table->string('maintenance_type');
            $table->date('scheduled_date');
            $table->string('frequency_type')->nullable();
            $table->unsignedInteger('frequency_interval')->nullable();
            $table->string('priority')->default('normal');
            $table->string('status')->default('planned');
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_worker_id')->nullable()->constrained('labour_workers')->nullOnDelete();
            $table->foreignId('assigned_team_id')->nullable()->constrained('labour_teams')->nullOnDelete();
            $table->foreignId('related_task_id')->nullable()->constrained('ops_tasks')->nullOnDelete();
            $table->text('instructions')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'farm_id', 'status', 'scheduled_date']);
        });

        Schema::create('asset_maintenance_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained('assets')->restrictOnDelete();
            $table->foreignId('maintenance_schedule_id')->nullable()->constrained('asset_maintenance_schedules')->nullOnDelete();
            $table->foreignId('related_task_id')->nullable()->constrained('ops_tasks')->nullOnDelete();
            $table->string('record_number')->unique();
            $table->string('maintenance_type');
            $table->date('maintenance_date');
            $table->string('status')->default('recorded');
            $table->foreignId('performed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('performed_by_worker_id')->nullable()->constrained('labour_workers')->nullOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('labour_teams')->nullOnDelete();
            $table->string('service_provider')->nullable();
            $table->text('problem_found')->nullable();
            $table->text('work_done');
            $table->text('parts_used_notes')->nullable();
            $table->foreignId('product_id')->nullable()->constrained('inventory_products')->nullOnDelete();
            $table->string('product_name_snapshot')->nullable();
            $table->decimal('quantity_used', 12, 2)->nullable();
            $table->string('quantity_unit')->nullable();
            $table->decimal('external_cost', 14, 2)->nullable();
            $table->string('currency')->nullable();
            $table->date('next_service_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'farm_id', 'maintenance_date']);
        });

        Schema::create('asset_breakdown_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained('assets')->restrictOnDelete();
            $table->foreignId('related_task_id')->nullable()->constrained('ops_tasks')->nullOnDelete();
            $table->string('breakdown_number')->unique();
            $table->date('breakdown_date');
            $table->string('issue_type');
            $table->string('severity');
            $table->string('status')->default('open');
            $table->text('description');
            $table->foreignId('reported_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reported_by_worker_id')->nullable()->constrained('labour_workers')->nullOnDelete();
            $table->foreignId('resolved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'farm_id', 'status', 'severity']);
        });

        Schema::create('asset_usage_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained('assets')->restrictOnDelete();
            $table->date('usage_date');
            $table->string('usage_type');
            $table->foreignId('related_task_id')->nullable()->constrained('ops_tasks')->nullOnDelete();
            $table->foreignId('related_irrigation_event_id')->nullable()->constrained('irrigation_events')->nullOnDelete();
            $table->foreignId('field_id')->nullable()->constrained('fields')->nullOnDelete();
            $table->foreignId('paddock_id')->nullable()->constrained('paddocks')->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->foreignId('used_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('used_by_worker_id')->nullable()->constrained('labour_workers')->nullOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('labour_teams')->nullOnDelete();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->decimal('meter_start', 12, 2)->nullable();
            $table->decimal('meter_end', 12, 2)->nullable();
            $table->decimal('usage_quantity', 12, 2)->nullable();
            $table->string('usage_unit')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'farm_id', 'usage_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_usage_records');
        Schema::dropIfExists('asset_breakdown_records');
        Schema::dropIfExists('asset_maintenance_records');
        Schema::dropIfExists('asset_maintenance_schedules');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('asset_categories');
    }
};
