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
        Schema::create('fumigation_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('farm_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('field_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('crop_cycle_id')->nullable()->constrained()->nullOnDelete();
            $table->string('target_pest'); // soil nematodes, storage insects, fungi, rodents
            $table->enum('fumigation_type', ['soil', 'storage', 'greenhouse'])->nullable();
            $table->foreignId('chemical_id')->nullable()->constrained('chemical_types')->onDelete('set null');
            $table->string('chemical_form')->nullable(); // Gas, Tablet
            $table->string('active_ingredient')->nullable();
            $table->string('mode_of_action')->nullable();
            $table->string('toxicity_level')->nullable(); // Critical for fumigation
            $table->decimal('quantity', 10, 2)->nullable();
            $table->string('unit')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('exposure_duration_hours')->nullable(); // e.g., 24-72 hours
            $table->integer('frequency_days')->nullable(); // if repeated
            $table->date('next_schedule_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->string('status')->default('scheduled');
            $table->decimal('temperature', 5, 2)->nullable(); // affects gas activity
            $table->decimal('humidity_level', 5, 2)->nullable(); // percentage
            $table->decimal('wind_speed', 5, 2)->nullable(); // for outdoor fumigation
            $table->string('location')->nullable(); // field, store, greenhouse
            $table->decimal('area_covered', 10, 2)->nullable(); // acres/hectares
            $table->decimal('volume_covered', 10, 2)->nullable(); // cubic meters for storage
            $table->string('dosage')->nullable(); // dosage & application
            $table->string('application_method')->nullable();
            $table->string('performed_by')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'scheduled_date']);
            $table->index(['status', 'scheduled_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fumigation_schedules');
    }
};