<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('irrigation_water_sources', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->string('source_type');
            $table->decimal('capacity', 12, 2)->nullable();
            $table->string('capacity_unit')->nullable();
            $table->string('location_description')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['farm_id', 'code']);
            $table->index(['organization_id', 'farm_id', 'status']);
        });

        Schema::create('irrigation_zones', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->foreignId('field_id')->nullable()->constrained('fields')->nullOnDelete();
            $table->foreignId('water_source_id')->nullable()->constrained('irrigation_water_sources')->nullOnDelete();
            $table->string('name');
            $table->string('code');
            $table->string('zone_type');
            $table->string('irrigation_method');
            $table->decimal('area', 12, 2)->nullable();
            $table->string('area_unit')->nullable();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['farm_id', 'code']);
            $table->index(['organization_id', 'farm_id', 'status']);
        });

        Schema::create('irrigation_schedules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('irrigation_zone_id')->constrained('irrigation_zones')->restrictOnDelete();
            $table->foreignId('field_id')->nullable()->constrained('fields')->nullOnDelete();
            $table->foreignId('crop_cycle_id')->nullable()->constrained('crop_cycles')->nullOnDelete();
            $table->foreignId('related_task_id')->nullable()->constrained('ops_tasks')->nullOnDelete();
            $table->string('schedule_number')->unique();
            $table->date('scheduled_date');
            $table->time('scheduled_start_time')->nullable();
            $table->time('scheduled_end_time')->nullable();
            $table->unsignedInteger('planned_duration_minutes')->nullable();
            $table->decimal('planned_water_volume', 12, 2)->nullable();
            $table->string('water_volume_unit')->nullable();
            $table->string('priority')->default('normal');
            $table->string('status')->default('planned');
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_worker_id')->nullable()->constrained('labour_workers')->nullOnDelete();
            $table->foreignId('assigned_team_id')->nullable()->constrained('labour_teams')->nullOnDelete();
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

        Schema::create('irrigation_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('irrigation_zone_id')->constrained('irrigation_zones')->restrictOnDelete();
            $table->foreignId('water_source_id')->nullable()->constrained('irrigation_water_sources')->nullOnDelete();
            $table->foreignId('field_id')->nullable()->constrained('fields')->nullOnDelete();
            $table->foreignId('crop_cycle_id')->nullable()->constrained('crop_cycles')->nullOnDelete();
            $table->foreignId('schedule_id')->nullable()->constrained('irrigation_schedules')->nullOnDelete();
            $table->foreignId('related_task_id')->nullable()->constrained('ops_tasks')->nullOnDelete();
            $table->string('event_number')->unique();
            $table->date('irrigation_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->decimal('water_volume', 12, 2)->nullable();
            $table->string('water_volume_unit')->nullable();
            $table->string('method')->nullable();
            $table->string('status')->default('recorded');
            $table->foreignId('performed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('performed_by_worker_id')->nullable()->constrained('labour_workers')->nullOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('labour_teams')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'farm_id', 'status', 'irrigation_date']);
        });

        Schema::create('irrigation_water_readings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('water_source_id')->nullable()->constrained('irrigation_water_sources')->cascadeOnDelete();
            $table->foreignId('irrigation_zone_id')->nullable()->constrained('irrigation_zones')->cascadeOnDelete();
            $table->date('reading_date');
            $table->string('reading_type');
            $table->decimal('value', 12, 2);
            $table->string('unit_of_measure');
            $table->foreignId('recorded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('recorded_by_worker_id')->nullable()->constrained('labour_workers')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('irrigation_issues', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('irrigation_zone_id')->nullable()->constrained('irrigation_zones')->nullOnDelete();
            $table->foreignId('water_source_id')->nullable()->constrained('irrigation_water_sources')->nullOnDelete();
            $table->foreignId('field_id')->nullable()->constrained('fields')->nullOnDelete();
            $table->foreignId('related_task_id')->nullable()->constrained('ops_tasks')->nullOnDelete();
            $table->string('issue_number')->unique();
            $table->date('issue_date');
            $table->string('issue_type');
            $table->string('severity');
            $table->string('status')->default('open');
            $table->text('description');
            $table->foreignId('reported_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reported_by_worker_id')->nullable()->constrained('labour_workers')->nullOnDelete();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'farm_id', 'status', 'severity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('irrigation_issues');
        Schema::dropIfExists('irrigation_water_readings');
        Schema::dropIfExists('irrigation_events');
        Schema::dropIfExists('irrigation_schedules');
        Schema::dropIfExists('irrigation_zones');
        Schema::dropIfExists('irrigation_water_sources');
    }
};
