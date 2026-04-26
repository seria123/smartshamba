<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crop_rotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crop_id')->constrained()->onDelete('cascade');
            $table->foreignId('previous_crop_id')->constrained('crops')->onDelete('cascade');
            $table->integer('sequence_order');
            $table->decimal('yield_benefit_percentage', 5, 2)->nullable();
            $table->text('benefits')->nullable();
            $table->text('risks')->nullable();
            $table->text('recommendations')->nullable();
            $table->timestamps();

            $table->index(['crop_id', 'previous_crop_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_rotations');
    }
};
