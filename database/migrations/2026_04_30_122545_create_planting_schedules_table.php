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
        Schema::create('planting_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('crop_id')->constrained()->onDelete('cascade');
            $table->foreignId('crop_cycle_id')->nullable()->constrained('crop_cycles')->onDelete('set null');
            $table->foreignId('field_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('farm_id')->nullable()->constrained()->onDelete('cascade');

            // Planting dates and windows
            $table->date('planting_date');
            $table->date('planting_window_start')->nullable()->comment('Earliest recommended planting');
            $table->date('planting_window_end')->nullable()->comment('Latest recommended planting');
            $table->date('expected_harvest_date')->nullable();
            $table->date('actual_harvest_date')->nullable();

            // Harvest tracking
            $table->decimal('estimated_quantity', 10, 2)->nullable()->comment('Estimated yield');
            $table->string('quantity_unit')->default('kg');
            $table->decimal('actual_quantity', 10, 2)->nullable()->comment('Actual harvested');
            $table->string('actual_quantity_unit')->nullable();

            // Status and tracking
            $table->enum('status', ['planned', 'planted', 'growing', 'ready_for_harvest', 'harvested', 'cancelled'])->default('planned');
            $table->enum('season', ['spring', 'summer', 'fall', 'winter', 'year_round'])->default('spring');
            $table->string('variety')->nullable();
            $table->text('notes')->nullable();

            // Progress tracking
            $table->integer('completion_percentage')->default(0);
            $table->string('current_stage')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index(['field_id', 'status']);
            $table->index(['farm_id', 'planting_date']);
            $table->index(['crop_id', 'season']);
            $table->index('status');
            $table->index('planting_date');
            $table->index('expected_harvest_date');
            $table->index(['crop_id', 'field_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planting_schedules');
    }
};
