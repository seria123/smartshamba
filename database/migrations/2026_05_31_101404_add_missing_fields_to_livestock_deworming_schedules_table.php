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
            // Animal Identification Details
            $table->date('date_of_birth')->nullable()->after('livestock_type_id');
            $table->decimal('weight', 10, 2)->nullable()->after('date_of_birth'); // Animal's weight
            
            // Health & Condition Tracking
            $table->decimal('current_weight', 10, 2)->nullable()->after('body_condition_score'); // Weight at time of treatment
            $table->date('previous_deworming_date')->nullable()->after('current_weight');
            
            // Administration Details
            $table->string('farm_location')->nullable()->after('supervised_by');
            
            // Notes & Observations
            $table->string('side_effects_observed')->nullable()->after('effectiveness');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('livestock_deworming_schedules', function (Blueprint $table) {
            $table->dropColumn(['date_of_birth', 'weight']);
            $table->dropColumn(['current_weight', 'previous_deworming_date']);
            $table->dropColumn(['farm_location']);
            $table->dropColumn(['side_effects_observed']);
        });
    }
};
