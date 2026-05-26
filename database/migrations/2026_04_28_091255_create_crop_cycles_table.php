<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crop_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained()->onDelete('cascade');
            $table->foreignId('crop_id')->constrained()->onDelete('cascade');
            $table->foreignId('farm_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('staff_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('crop_name')->nullable();
            $table->string('variety')->nullable();
            $table->string('category')->nullable();
            $table->string('season')->nullable();
            $table->date('start_date')->nullable();
            $table->date('expected_harvest_date')->nullable();
            $table->string('current_stage')->nullable();
            $table->string('status')->default('planned');
            
            // Land & Soil Requirements
            $table->string('soil_type_override')->nullable();
            $table->decimal('ph_level', 3, 1)->nullable();
            $table->foreignId('previous_crop_cycle_id')->nullable()->constrained()->onDelete('set null');
            $table->string('water_source_override')->nullable();
            
            // Water & Irrigation
            $table->string('irrigation_type')->nullable();
            $table->text('irrigation_schedule')->nullable();
            $table->string('drainage')->nullable();
            
            // Planting Details
            $table->string('seed_batch_number')->nullable();
            $table->decimal('seed_quantity', 10, 2)->nullable();
            $table->integer('seedling_quantity')->nullable();
            $table->string('planting_method')->nullable();
            $table->decimal('area_planted', 10, 2)->nullable(); // in square meters
            $table->integer('spacing_row')->nullable(); // in cm
            $table->integer('spacing_plant')->nullable(); // in cm
            $table->integer('plant_population')->nullable();
            $table->decimal('germination_rate', 5, 2)->nullable(); // percentage
            $table->decimal('survival_rate', 5, 2)->nullable(); // percentage
            $table->integer('survival_count')->nullable();
            $table->integer('planting_labor_workers')->nullable();
            $table->decimal('planting_labor_cost', 10, 2)->nullable();
            $table->text('planting_notes')->nullable();
            
            // Growth Metrics
            $table->decimal('average_plant_height', 5, 2)->nullable(); // in cm
            $table->decimal('yield_estimate', 10, 2)->nullable(); // in kg
            
            // Cost Tracking
            $table->decimal('input_cost_total', 10, 2)->default(0);
            $table->decimal('labor_cost_total', 10, 2)->default(0);
            $table->decimal('equipment_cost_total', 10, 2)->default(0);
            $table->decimal('total_cost_of_production', 10, 2)->default(0);
            $table->decimal('revenue_from_sales', 10, 2)->default(0);
            $table->decimal('profit_loss', 10, 2)->default(0);
            
            // Cycle Management
            $table->text('notes_for_improvement')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_cycles');
    }
};
