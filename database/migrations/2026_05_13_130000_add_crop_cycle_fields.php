<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crop_cycles', function (Blueprint $table) {
            $table->string('code')->nullable()->after('id')->unique()->comment('Unique identifier e.g. TOM-GH2-MAR2026');
            $table->decimal('area_planted', 10, 4)->nullable()->comment('Area in hectares/acres');
            $table->string('planting_method')->nullable()->comment('direct_seeding, transplanting, cuttings');
            $table->date('planned_start_date')->nullable()->comment('When preparation is expected to begin');
            $table->foreignId('staff_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['planned', 'active', 'completed', 'cancelled'])->default('planned');
            
            // Soil testing
            $table->decimal('soil_test_ph', 4, 2)->nullable();
            $table->decimal('soil_test_nitrogen', 8, 2)->nullable();
            $table->decimal('soil_test_phosphorus', 8, 2)->nullable();
            $table->decimal('soil_test_potassium', 8, 2)->nullable();
            $table->text('soil_test_recommendations')->nullable();
            
            // Manure/compost
            $table->decimal('manure_quantity', 10, 2)->nullable();
            $table->decimal('manure_cost', 10, 2)->nullable();
            $table->string('manure_source')->nullable();
            
            // Basal fertilizer
            $table->decimal('basal_fertilizer_dap', 10, 2)->nullable();
            $table->decimal('basal_fertilizer_npk', 10, 2)->nullable();
            $table->decimal('basal_fertilizer_lime', 10, 2)->nullable();
            
            // Irrigation setup
            $table->string('irrigation_setup')->nullable();
            
            // Preparation labor
            $table->integer('prep_labor_workers')->nullable();
            $table->decimal('prep_labor_hours', 8, 2)->nullable();
            $table->decimal('prep_labor_cost', 10, 2)->nullable();
            
            // Machinery
            $table->decimal('machinery_tractor', 8, 2)->nullable();
            $table->decimal('machinery_pump', 8, 2)->nullable();
            $table->decimal('machinery_sprayer', 8, 2)->nullable();
            $table->text('machinery_notes')->nullable();
            
            // Planting Details
            $table->string('seed_batch_number')->nullable()->comment('Seed lot number or nursery batch');
            $table->decimal('seed_quantity', 10, 2)->nullable()->comment('Quantity of seed planted');
            $table->integer('seedling_quantity')->nullable()->comment('Number of seedlings planted');
            $table->integer('spacing_row')->nullable()->comment('Row spacing in cm');
            $table->integer('spacing_plant')->nullable()->comment('Plant spacing in cm');
            $table->integer('plant_population')->nullable()->comment('Expected number of plants');
            $table->decimal('germination_rate', 5, 2)->nullable()->comment('Germination rate percentage');
            $table->decimal('survival_rate', 5, 2)->nullable()->comment('Survival rate percentage');
            $table->integer('planting_labor_workers')->nullable();
            $table->decimal('planting_labor_cost', 10, 2)->nullable();
            $table->text('planting_notes')->nullable();
            
            $table->index('code');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('crop_cycles', function (Blueprint $table) {
            $table->dropColumn([
                'code', 'area_planted', 'planting_method', 'planned_start_date', 'staff_id', 'status',
                'soil_test_ph', 'soil_test_nitrogen', 'soil_test_phosphorus', 'soil_test_potassium',
                'soil_test_recommendations', 'manure_quantity', 'manure_cost', 'manure_source',
                'basal_fertilizer_dap', 'basal_fertilizer_npk', 'basal_fertilizer_lime',
                'irrigation_setup', 'prep_labor_workers', 'prep_labor_hours', 'prep_labor_cost',
                'machinery_tractor', 'machinery_pump', 'machinery_sprayer', 'machinery_notes',
                // Planting Details
                'seed_batch_number', 'seed_quantity', 'seedling_quantity', 'spacing_row',
                'spacing_plant', 'plant_population', 'germination_rate', 'survival_rate',
                'planting_labor_workers', 'planting_labor_cost', 'planting_notes',
            ]);
        });
    }
};