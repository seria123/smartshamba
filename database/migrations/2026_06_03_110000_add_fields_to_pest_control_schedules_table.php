<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pest_control_schedules', function (Blueprint $table) {
            $table->foreignId('crop_id')->nullable()->after('field_id')->constrained()->nullOnDelete();
            $table->string('threat_level')->nullable()->after('pest_name');
            $table->decimal('cost', 10, 2)->nullable()->after('treatment_notes');
            $table->string('treatment_method')->nullable()->after('cost');
            $table->string('crop_type')->nullable()->after('crop_cycle_id');
            $table->string('crop_variety')->nullable()->after('crop_type');
            $table->date('planting_date')->nullable()->after('crop_variety');
            $table->string('growth_stage')->nullable()->after('planting_date');
            $table->integer('frequency_days')->nullable()->after('scheduled_date');
            $table->string('growth_stage_trigger')->nullable()->after('frequency_days');
            $table->integer('rei_hours')->nullable()->after('growth_stage_trigger');
            $table->integer('phi_days')->nullable()->after('rei_hours');
            $table->decimal('temperature', 5, 2)->nullable()->after('phi_days');
            $table->boolean('rain_forecast')->nullable()->after('temperature');
            $table->decimal('wind_speed', 5, 2)->nullable()->after('rain_forecast');
            $table->string('mixing_ratio')->nullable()->after('wind_speed');
            $table->decimal('water_volume', 10, 2)->nullable()->after('mixing_ratio');
            $table->string('equipment')->nullable()->after('water_volume');
            $table->decimal('area_covered', 10, 2)->nullable()->after('equipment');
            $table->string('operator')->nullable()->after('area_covered');
            $table->boolean('gear_gloves')->default(false)->after('operator');
            $table->boolean('gear_mask')->default(false)->after('gear_gloves');
            $table->boolean('gear_overalls')->default(false)->after('gear_mask');
            $table->text('safety_notes')->nullable()->after('gear_overalls');
        });
    }

    public function down(): void
    {
        Schema::table('pest_control_schedules', function (Blueprint $table) {
            $table->dropColumn([
                'crop_id', 'threat_level', 'cost', 'treatment_method',
                'crop_type', 'crop_variety', 'planting_date', 'growth_stage',
                'frequency_days', 'growth_stage_trigger', 'rei_hours', 'phi_days',
                'temperature', 'rain_forecast', 'wind_speed', 'mixing_ratio',
                'water_volume', 'equipment', 'area_covered', 'operator',
                'gear_gloves', 'gear_mask', 'gear_overalls', 'safety_notes',
            ]);
        });
    }
};