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
        Schema::create('weather_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained()->onDelete('cascade');
            $table->dateTime('recorded_at');
            $table->decimal('temperature', 5, 2)->nullable(); // Celsius
            $table->decimal('humidity', 5, 2)->nullable(); // Percentage
            $table->decimal('precipitation', 6, 2)->nullable(); // mm
            $table->decimal('wind_speed', 6, 2)->nullable(); // m/s
            $table->decimal('wind_direction', 5, 2)->nullable(); // Degrees
            $table->decimal('pressure', 7, 2)->nullable(); // hPa
            $table->decimal('uv_index', 4, 2)->nullable();
            $table->decimal('cloud_cover', 5, 2)->nullable(); // Percentage
            $table->decimal('dew_point', 5, 2)->nullable();
            $table->string('weather_condition')->nullable(); // sunny, cloudy, rainy, etc.
            $table->decimal('feels_like', 5, 2)->nullable();
            $table->decimal('visibility', 6, 2)->nullable(); // km
            $table->timestamps();

            $table->index(['farm_id', 'recorded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_data');
    }
};
