<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crop_varieties', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('crop_id')->constrained('crop_crops')->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->unsignedInteger('expected_growing_days')->nullable();
            $table->decimal('seed_rate', 12, 2)->nullable();
            $table->string('seed_rate_unit')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->unique(['crop_id', 'code']);
            $table->index(['crop_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_varieties');
    }
};
