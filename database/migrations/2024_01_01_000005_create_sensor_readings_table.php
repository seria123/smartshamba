<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sensor_id')->constrained()->onDelete('cascade');
            $table->decimal('soil_moisture', 5, 2)->nullable(); // percentage
            $table->decimal('temperature', 5, 2)->nullable(); // celsius
            $table->decimal('humidity', 5, 2)->nullable(); // percentage
            $table->decimal('soil_ph', 3, 2)->nullable(); // pH scale 0-14
            $table->timestamp('timestamp')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensor_readings');
    }
};
