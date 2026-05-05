<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crop_treatment_applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('crop_cycle_id')->constrained('crop_cycles')->cascadeOnDelete();
            $table->foreignId('field_id')->constrained('fields')->restrictOnDelete();
            $table->foreignId('crop_activity_id')->nullable()->constrained('crop_activities')->nullOnDelete();
            $table->foreignId('task_id')->nullable()->constrained('ops_tasks')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('inventory_products')->nullOnDelete();
            $table->string('application_type');
            $table->date('application_date');
            $table->string('product_name_snapshot')->nullable();
            $table->string('product_code_snapshot')->nullable();
            $table->decimal('quantity_used', 12, 2)->nullable();
            $table->string('quantity_unit')->nullable();
            $table->string('application_rate')->nullable();
            $table->string('target_problem')->nullable();
            $table->string('method')->nullable();
            $table->text('weather_notes')->nullable();
            $table->unsignedInteger('phi_days')->nullable();
            $table->unsignedInteger('rei_hours')->nullable();
            $table->foreignId('applied_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_treatment_applications');
    }
};
