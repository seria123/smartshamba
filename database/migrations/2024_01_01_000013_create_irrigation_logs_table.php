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
        Schema::create('irrigation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('irrigation_zone_id')->constrained()->onDelete('cascade');
            $table->foreignId('triggered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type'); // scheduled, manual, triggered, stopped
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->decimal('water_used_liters', 10, 2)->nullable();
            $table->decimal('soil_moisture_before', 5, 2)->nullable();
            $table->decimal('soil_moisture_after', 5, 2)->nullable();
            $table->string('status')->default('running'); // running, completed, failed, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['irrigation_zone_id', 'started_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('irrigation_logs');
    }
};