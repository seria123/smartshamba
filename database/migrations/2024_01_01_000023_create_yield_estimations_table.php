<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('yield_estimations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crop_id')->constrained()->onDelete('cascade');
            $table->foreignId('farmer_id')->constrained()->onDelete('cascade');
            $table->foreignId('field_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('hectares', 10, 2)->nullable();
            $table->string('season');
            $table->integer('year');
            $table->decimal('estimated_yield', 10, 2)->nullable();
            $table->decimal('actual_yield', 10, 2)->nullable();
            $table->decimal('yield_per_hectare', 10, 2)->nullable();
            $table->string('yield_unit')->default('kg');
            $table->decimal('estimated_income', 12, 2)->nullable();
            $table->decimal('actual_income', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['planned', 'in_progress', 'harvested', 'failed'])->default('planned');
            $table->timestamps();

            $table->index('farmer_id');
            $table->index(['crop_id', 'season']);
            $table->index(['farmer_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('yield_estimations');
    }
};
