<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('harvests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crop_id')->constrained()->onDelete('cascade');
            $table->foreignId('field_id')->constrained()->onDelete('cascade');
            $table->foreignId('farm_id')->constrained()->onDelete('cascade');
            $table->foreignId('crop_cycle_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('harvest_date');
            $table->string('harvest_number')->nullable();
            $table->decimal('quantity_harvested', 12, 2)->default(0);
            $table->string('unit')->default('kg');
            $table->enum('quality_grade', ['grade_a', 'grade_b', 'grade_c', 'reject'])->default('grade_b');
            $table->decimal('quality_percentage', 5, 2)->default(100);
            $table->decimal('grade_1_quantity', 12, 2)->default(0);
            $table->decimal('grade_2_quantity', 12, 2)->default(0);
            $table->decimal('rejects_quantity', 12, 2)->default(0);
            $table->decimal('loss_quantity', 12, 2)->default(0);
            $table->string('loss_reason')->nullable();
            $table->decimal('loss_percentage', 5, 2)->default(0);
            $table->enum('destination', ['store', 'sold_directly', 'processing'])->default('store');
            $table->string('buyer_reference')->nullable();
            $table->enum('storage_location', ['field', 'barn', 'warehouse', 'cold_storage', 'sold_immediately'])->default('warehouse');
            $table->string('storage_details')->nullable();
            $table->decimal('moisture_content', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['crop_id', 'harvest_date']);
            $table->index(['field_id', 'harvest_date']);
            $table->index(['crop_cycle_id', 'harvest_date']);
            $table->index('harvest_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harvests');
    }
};
