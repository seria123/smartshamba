<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crop_cycles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('field_id')->constrained('fields')->restrictOnDelete();
            $table->foreignId('crop_id')->constrained('crop_crops')->restrictOnDelete();
            $table->foreignId('variety_id')->nullable()->constrained('crop_varieties')->nullOnDelete();
            $table->foreignId('season_id')->nullable()->constrained('crop_seasons')->nullOnDelete();
            $table->string('cycle_number')->unique();
            $table->string('name');
            $table->date('planned_start_date')->nullable();
            $table->date('actual_planting_date')->nullable();
            $table->date('expected_harvest_date')->nullable();
            $table->decimal('area_planted', 12, 2)->nullable();
            $table->string('area_unit')->nullable();
            $table->unsignedInteger('plant_population')->nullable();
            $table->string('spacing')->nullable();
            $table->string('seed_source')->nullable();
            $table->foreignId('manager_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('planned');
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('closure_notes')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'farm_id', 'status']);
            $table->index(['crop_id', 'variety_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_cycles');
    }
};
