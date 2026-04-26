<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->text('description')->nullable();
            $table->string('variety')->nullable();
            $table->integer('days_to_maturity')->nullable();
            $table->decimal('average_yield_per_hectare', 10, 2)->nullable();
            $table->string('yield_unit')->default('kg');
            $table->string('season_type');
            $table->json('growth_stages')->nullable();
            $table->json('soil_requirements')->nullable();
            $table->json('water_requirements')->nullable();
            $table->json('pest_vulnerabilities')->nullable();
            $table->decimal('min_temperature', 5, 2)->nullable();
            $table->decimal('max_temperature', 5, 2)->nullable();
            $table->decimal('optimal_ph_min', 4, 2)->nullable();
            $table->decimal('optimal_ph_max', 4, 2)->nullable();
            $table->timestamps();

            $table->unique(['name', 'variety']);
            $table->index('category');
            $table->index('season_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crops');
    }
};
