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
        Schema::create('irrigation_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('controller_id')->nullable();
            $table->string('valve_id')->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->decimal('flow_rate_lph', 10, 2)->nullable();
            $table->integer('duration_minutes')->default(30);
            $table->time('start_time')->nullable();
            $table->enum('schedule_type', ['manual', 'scheduled', 'smart'])->default('manual');
            $table->json('schedule_days')->nullable(); // Days of week for scheduled irrigation
            $table->decimal('soil_moisture_threshold', 5, 2)->nullable(); // Auto-irrigation threshold
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('irrigation_zones');
    }
};
