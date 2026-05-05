<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crop_planting_details', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('crop_activity_id')->constrained('crop_activities')->cascadeOnDelete();
            $table->string('planting_method')->nullable();
            $table->decimal('seed_quantity', 12, 2)->nullable();
            $table->string('seed_unit')->nullable();
            $table->unsignedInteger('plant_population')->nullable();
            $table->string('spacing')->nullable();
            $table->string('nursery_source')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_planting_details');
    }
};
