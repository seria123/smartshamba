<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spraying_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crop_cycle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('field_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('chemical_type');
            $table->string('chemical_name')->nullable();
            $table->decimal('quantity', 10, 2)->nullable();
            $table->string('unit')->default('liters');
            $table->date('application_date');
            $table->string('growth_stage')->nullable();
            $table->string('application_method')->nullable();
            $table->string('weather_conditions')->nullable();
            $table->string('target_pest')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spraying_applications');
    }
};