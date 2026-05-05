<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crop_loss_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('crop_cycle_id')->constrained('crop_cycles')->cascadeOnDelete();
            $table->foreignId('field_id')->constrained('fields')->restrictOnDelete();
            $table->foreignId('crop_activity_id')->nullable()->constrained('crop_activities')->nullOnDelete();
            $table->date('loss_date');
            $table->string('loss_type');
            $table->decimal('estimated_quantity', 12, 2)->nullable();
            $table->string('quantity_unit')->nullable();
            $table->decimal('affected_area', 12, 2)->nullable();
            $table->string('affected_area_unit')->nullable();
            $table->string('cause')->nullable();
            $table->string('severity')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_loss_records');
    }
};
