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
            $table->string('breed')->nullable();
            $table->string('group_herd_pen')->nullable();
            $table->date('date_of_birth')->nullable(); // For Animal Identification Details
            $table->decimal('weight', 10, 2)->nullable(); // Weight from Animal Identification Details
            
            // Deworming Treatment Details
            $table->string('manufacturer_brand')->nullable();
            $table->date('expiry_date')->nullable();
            
            // Schedule & Timing
            $table->string('deworming_frequency')->nullable()->comment('Monthly, Quarterly, Custom');
            $table->boolean('reminder_toggle')->default(false);
            $table->string('reminder_method')->nullable()->comment('SMS, App Notification, Email');
            
            // Health & Condition Tracking
            $table->decimal('body_condition_score', 3, 1)->nullable(); // e.g., 1.0 to 5.0
            $table->text('signs_of_infection')->nullable()->comment('JSON array of signs like diarrhea, weight loss, dull coat');
            $table->text('resistance_history')->nullable()->comment('JSON array or text about resistance history');
            $table->decimal('current_weight', 10, 2)->nullable(); // Current weight at time of treatment
            $table->date('previous_deworming_date')->nullable();
            
            // Administration Details
            $table->string('administration_method')->nullable()->comment('Drenching, Injection, Feed mix');
            $table->string('supervised_by')->nullable();
            $table->string('farm_location')->nullable();
            
            // Notes & Observations
            $table->string('animal_reaction')->nullable()->comment('Normal, Weak, Vomiting, etc.');
            $table->string('effectiveness')->nullable()->comment('Improved, No change, Worse');
            $table->string('side_effects_observed')->nullable()->comment('Side effects observed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('livestock_deworming_schedules', function (Blueprint $table) {
            $table->dropColumn(['breed', 'group_herd_pen', 'date_of_birth', 'weight']);
            $table->dropColumn(['manufacturer_brand', 'expiry_date']);
            $table->dropColumn(['deworming_frequency', 'reminder_toggle', 'reminder_method']);
            $table->dropColumn(['body_condition_score', 'signs_of_infection', 'resistance_history', 'current_weight', 'previous_deworming_date']);
            $table->dropColumn(['administration_method', 'supervised_by', 'farm_location']);
            $table->dropColumn(['animal_reaction', 'effectiveness', 'side_effects_observed']);
        });
    }
};
