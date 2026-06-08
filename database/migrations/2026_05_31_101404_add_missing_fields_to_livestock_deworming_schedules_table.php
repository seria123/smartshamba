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
        Schema::table('livestock_deworming_schedules', function (Blueprint $table) {
            $hasColumn = fn (string $column): bool => Schema::hasColumn('livestock_deworming_schedules', $column);

            // Animal Identification Details
            if (! $hasColumn('date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('livestock_type_id');
            }
            if (! $hasColumn('weight')) {
                $table->decimal('weight', 10, 2)->nullable()->after('date_of_birth');
            }
            
            // Health & Condition Tracking
            if (! $hasColumn('current_weight')) {
                $table->decimal('current_weight', 10, 2)->nullable()->after('body_condition_score');
            }
            if (! $hasColumn('previous_deworming_date')) {
                $table->date('previous_deworming_date')->nullable()->after('current_weight');
            }
            
            // Administration Details
            if (! $hasColumn('farm_location')) {
                $table->string('farm_location')->nullable()->after('supervised_by');
            }
            
            // Notes & Observations
            if (! $hasColumn('side_effects_observed')) {
                $table->string('side_effects_observed')->nullable()->after('effectiveness');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('livestock_deworming_schedules', function (Blueprint $table) {
            foreach (['date_of_birth', 'weight', 'current_weight', 'previous_deworming_date', 'farm_location', 'side_effects_observed'] as $column) {
                if (Schema::hasColumn('livestock_deworming_schedules', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
