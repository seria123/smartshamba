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
            if (! $hasColumn('breed')) {
                $table->string('breed')->nullable();
            }
            if (! $hasColumn('group_herd_pen')) {
                $table->string('group_herd_pen')->nullable();
            }
            if (! $hasColumn('date_of_birth')) {
                $table->date('date_of_birth')->nullable();
            }
            if (! $hasColumn('weight')) {
                $table->decimal('weight', 10, 2)->nullable();
            }
            
            // Deworming Treatment Details
            if (! $hasColumn('manufacturer_brand')) {
                $table->string('manufacturer_brand')->nullable();
            }
            if (! $hasColumn('expiry_date')) {
                $table->date('expiry_date')->nullable();
            }
            
            // Schedule & Timing
            if (! $hasColumn('deworming_frequency')) {
                $table->string('deworming_frequency')->nullable()->comment('Monthly, Quarterly, Custom');
            }
            if (! $hasColumn('reminder_toggle')) {
                $table->boolean('reminder_toggle')->default(false);
            }
            if (! $hasColumn('reminder_method')) {
                $table->string('reminder_method')->nullable()->comment('SMS, App Notification, Email');
            }
            
            // Health & Condition Tracking
            if (! $hasColumn('body_condition_score')) {
                $table->decimal('body_condition_score', 3, 1)->nullable();
            }
            if (! $hasColumn('signs_of_infection')) {
                $table->text('signs_of_infection')->nullable()->comment('JSON array of signs like diarrhea, weight loss, dull coat');
            }
            if (! $hasColumn('resistance_history')) {
                $table->text('resistance_history')->nullable()->comment('JSON array or text about resistance history');
            }
            if (! $hasColumn('current_weight')) {
                $table->decimal('current_weight', 10, 2)->nullable();
            }
            if (! $hasColumn('previous_deworming_date')) {
                $table->date('previous_deworming_date')->nullable();
            }
            
            // Administration Details
            if (! $hasColumn('administration_method')) {
                $table->string('administration_method')->nullable()->comment('Drenching, Injection, Feed mix');
            }
            if (! $hasColumn('supervised_by')) {
                $table->string('supervised_by')->nullable();
            }
            if (! $hasColumn('farm_location')) {
                $table->string('farm_location')->nullable();
            }
            
            // Notes & Observations
            if (! $hasColumn('animal_reaction')) {
                $table->string('animal_reaction')->nullable()->comment('Normal, Weak, Vomiting, etc.');
            }
            if (! $hasColumn('effectiveness')) {
                $table->string('effectiveness')->nullable()->comment('Improved, No change, Worse');
            }
            if (! $hasColumn('side_effects_observed')) {
                $table->string('side_effects_observed')->nullable()->comment('Side effects observed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('livestock_deworming_schedules', function (Blueprint $table) {
            $columns = [
                'breed', 'group_herd_pen', 'date_of_birth', 'weight',
                'manufacturer_brand', 'expiry_date',
                'deworming_frequency', 'reminder_toggle', 'reminder_method',
                'body_condition_score', 'signs_of_infection', 'resistance_history', 'current_weight', 'previous_deworming_date',
                'administration_method', 'supervised_by', 'farm_location',
                'animal_reaction', 'effectiveness', 'side_effects_observed',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('livestock_deworming_schedules', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
