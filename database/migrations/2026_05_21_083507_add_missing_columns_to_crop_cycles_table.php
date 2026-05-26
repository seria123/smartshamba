<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('crop_cycles', function (Blueprint $table) {
            // Check if columns exist before adding them (for safety)
            if (!Schema::hasColumn('crop_cycles', 'farm_id')) {
                $table->foreignId('farm_id')->nullable()->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('crop_cycles', 'staff_id')) {
                $table->foreignId('staff_id')->nullable()->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('crop_cycles', 'crop_name')) {
                $table->string('crop_name')->nullable();
            }
            if (!Schema::hasColumn('crop_cycles', 'variety')) {
                $table->string('variety')->nullable();
            }
            if (!Schema::hasColumn('crop_cycles', 'category')) {
                $table->string('category')->nullable();
            }
            if (!Schema::hasColumn('crop_cycles', 'season')) {
                $table->string('season')->nullable();
            }
            if (!Schema::hasColumn('crop_cycles', 'status')) {
                $table->string('status')->default('planned');
            }
            
            // Land & Soil Requirements
            if (!Schema::hasColumn('crop_cycles', 'soil_type_override')) {
                $table->string('soil_type_override')->nullable();
            }
            if (!Schema::hasColumn('crop_cycles', 'ph_level')) {
                $table->decimal('ph_level', 3, 1)->nullable();
            }
            if (!Schema::hasColumn('crop_cycles', 'previous_crop_cycle_id')) {
                $table->foreignId('previous_crop_cycle_id')->nullable()->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('crop_cycles', 'water_source_override')) {
                $table->string('water_source_override')->nullable();
            }
            
            // Water & Irrigation
            if (!Schema::hasColumn('crop_cycles', 'irrigation_type')) {
                $table->string('irrigation_type')->nullable();
            }
            if (!Schema::hasColumn('crop_cycles', 'irrigation_schedule')) {
                $table->text('irrigation_schedule')->nullable();
            }
            if (!Schema::hasColumn('crop_cycles', 'drainage')) {
                $table->string('drainage')->nullable();
            }
            
            // Planting Details
            if (!Schema::hasColumn('crop_cycles', 'seed_batch_number')) {
                $table->string('seed_batch_number')->nullable();
            }
            if (!Schema::hasColumn('crop_cycles', 'seed_quantity')) {
                $table->decimal('seed_quantity', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('crop_cycles', 'seedling_quantity')) {
                $table->integer('seedling_quantity')->nullable();
            }
            if (!Schema::hasColumn('crop_cycles', 'planting_method')) {
                $table->string('planting_method')->nullable();
            }
            if (!Schema::hasColumn('crop_cycles', 'area_planted')) {
                $table->decimal('area_planted', 10, 2)->nullable(); // in square meters
            }
            if (!Schema::hasColumn('crop_cycles', 'spacing_row')) {
                $table->integer('spacing_row')->nullable(); // in cm
            }
            if (!Schema::hasColumn('crop_cycles', 'spacing_plant')) {
                $table->integer('spacing_plant')->nullable(); // in cm
            }
            if (!Schema::hasColumn('crop_cycles', 'plant_population')) {
                $table->integer('plant_population')->nullable();
            }
            if (!Schema::hasColumn('crop_cycles', 'germination_rate')) {
                $table->decimal('germination_rate', 5, 2)->nullable(); // percentage
            }
            if (!Schema::hasColumn('crop_cycles', 'survival_rate')) {
                $table->decimal('survival_rate', 5, 2)->nullable(); // percentage
            }
            if (!Schema::hasColumn('crop_cycles', 'survival_count')) {
                $table->integer('survival_count')->nullable();
            }
            if (!Schema::hasColumn('crop_cycles', 'planting_labor_workers')) {
                $table->integer('planting_labor_workers')->nullable();
            }
            if (!Schema::hasColumn('crop_cycles', 'planting_labor_cost')) {
                $table->decimal('planting_labor_cost', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('crop_cycles', 'planting_notes')) {
                $table->text('planting_notes')->nullable();
            }
            
            // Growth Metrics
            if (!Schema::hasColumn('crop_cycles', 'average_plant_height')) {
                $table->decimal('average_plant_height', 5, 2)->nullable(); // in cm
            }
            if (!Schema::hasColumn('crop_cycles', 'yield_estimate')) {
                $table->decimal('yield_estimate', 10, 2)->nullable(); // in kg
            }
            
            // Cost Tracking
            if (!Schema::hasColumn('crop_cycles', 'input_cost_total')) {
                $table->decimal('input_cost_total', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('crop_cycles', 'labor_cost_total')) {
                $table->decimal('labor_cost_total', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('crop_cycles', 'equipment_cost_total')) {
                $table->decimal('equipment_cost_total', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('crop_cycles', 'total_cost_of_production')) {
                $table->decimal('total_cost_of_production', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('crop_cycles', 'revenue_from_sales')) {
                $table->decimal('revenue_from_sales', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('crop_cycles', 'profit_loss')) {
                $table->decimal('profit_loss', 10, 2)->default(0);
            }
            
            // Cycle Management
            if (!Schema::hasColumn('crop_cycles', 'notes_for_improvement')) {
                $table->text('notes_for_improvement')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove columns in reverse order (if they exist)
        if (Schema::hasColumn('crop_cycles', 'notes_for_improvement')) {
            $table->dropColumn('notes_for_improvement');
        }
        if (Schema::hasColumn('crop_cycles', 'profit_loss')) {
            $table->dropColumn('profit_loss');
        }
        if (Schema::hasColumn('crop_cycles', 'revenue_from_sales')) {
            $table->dropColumn('revenue_from_sales');
        }
        if (Schema::hasColumn('crop_cycles', 'total_cost_of_production')) {
            $table->dropColumn('total_cost_of_production');
        }
        if (Schema::hasColumn('crop_cycles', 'equipment_cost_total')) {
            $table->dropColumn('equipment_cost_total');
        }
        if (Schema::hasColumn('crop_cycles', 'labor_cost_total')) {
            $table->dropColumn('labor_cost_total');
        }
        if (Schema::hasColumn('crop_cycles', 'input_cost_total')) {
            $table->dropColumn('input_cost_total');
        }
        if (Schema::hasColumn('crop_cycles', 'yield_estimate')) {
            $table->dropColumn('yield_estimate');
        }
        if (Schema::hasColumn('crop_cycles', 'average_plant_height')) {
            $table->dropColumn('average_plant_height');
        }
        if (Schema::hasColumn('crop_cycles', 'planting_notes')) {
            $table->dropColumn('planting_notes');
        }
        if (Schema::hasColumn('crop_cycles', 'planting_labor_cost')) {
            $table->dropColumn('planting_labor_cost');
        }
        if (Schema::hasColumn('crop_cycles', 'planting_labor_workers')) {
            $table->dropColumn('planting_labor_workers');
        }
        if (Schema::hasColumn('crop_cycles', 'survival_count')) {
            $table->dropColumn('survival_count');
        }
        if (Schema::hasColumn('crop_cycles', 'survival_rate')) {
            $table->dropColumn('survival_rate');
        }
        if (Schema::hasColumn('crop_cycles', 'germination_rate')) {
            $table->dropColumn('germination_rate');
        }
        if (Schema::hasColumn('crop_cycles', 'spacing_plant')) {
            $table->dropColumn('spacing_plant');
        }
        if (Schema::hasColumn('crop_cycles', 'spacing_row')) {
            $table->dropColumn('spacing_row');
        }
        if (Schema::hasColumn('crop_cycles', 'area_planted')) {
            $table->dropColumn('area_planted');
        }
        if (Schema::hasColumn('crop_cycles', 'planting_method')) {
            $table->dropColumn('planting_method');
        }
        if (Schema::hasColumn('crop_cycles', 'seedling_quantity')) {
            $table->dropColumn('seedling_quantity');
        }
        if (Schema::hasColumn('crop_cycles', 'seed_quantity')) {
            $table->dropColumn('seed_quantity');
        }
        if (Schema::hasColumn('crop_cycles', 'seed_batch_number')) {
            $table->dropColumn('seed_batch_number');
        }
        if (Schema::hasColumn('crop_cycles', 'drainage')) {
            $table->dropColumn('drainage');
        }
        if (Schema::hasColumn('crop_cycles', 'irrigation_schedule')) {
            $table->dropColumn('irrigation_schedule');
        }
        if (Schema::hasColumn('crop_cycles', 'irrigation_type')) {
            $table->dropColumn('irrigation_type');
        }
        if (Schema::hasColumn('crop_cycles', 'water_source_override')) {
            $table->dropColumn('water_source_override');
        }
        if (Schema::hasColumn('crop_cycles', 'previous_crop_cycle_id')) {
            $table->dropColumn('previous_crop_cycle_id');
        }
        if (Schema::hasColumn('crop_cycles', 'ph_level')) {
            $table->dropColumn('ph_level');
        }
        if (Schema::hasColumn('crop_cycles', 'soil_type_override')) {
            $table->dropColumn('soil_type_override');
        }
        if (Schema::hasColumn('crop_cycles', 'status')) {
            $table->dropColumn('status');
        }
        if (Schema::hasColumn('crop_cycles', 'season')) {
            $table->dropColumn('season');
        }
        if (Schema::hasColumn('crop_cycles', 'category')) {
            $table->dropColumn('category');
        }
        if (Schema::hasColumn('crop_cycles', 'variety')) {
            $table->dropColumn('variety');
        }
        if (Schema::hasColumn('crop_cycles', 'crop_name')) {
            $table->dropColumn('crop_name');
        }
        if (Schema::hasColumn('crop_cycles', 'staff_id')) {
            $table->dropColumn('staff_id');
        }
        if (Schema::hasColumn('crop_cycles', 'farm_id')) {
            $table->dropColumn('farm_id');
        }
    }
};
