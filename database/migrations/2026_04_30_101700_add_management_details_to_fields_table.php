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
        Schema::table('fields', function (Blueprint $table) {
            // Crop management details
            $table->string('seed_quantity_estimate')->nullable()->after('description');
            $table->string('fertilizer_type')->nullable()->after('seed_quantity_estimate');
            $table->string('fertilizer_amount')->nullable()->after('fertilizer_type');
            $table->text('pest_control_recommendations')->nullable()->after('fertilizer_amount');
            
            // Livestock management details
            $table->text('feed_requirements')->nullable()->after('pest_control_recommendations');
            $table->text('vaccination_schedule')->nullable()->after('feed_requirements');
            $table->text('housing_requirements')->nullable()->after('vaccination_schedule');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fields', function (Blueprint $table) {
            $table->dropColumn([
                'seed_quantity_estimate',
                'fertilizer_type',
                'fertilizer_amount',
                'pest_control_recommendations',
                'feed_requirements',
                'vaccination_schedule',
                'housing_requirements'
            ]);
        });
    }
};
