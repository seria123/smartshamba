<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crop_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained()->onDelete('cascade');
            $table->foreignId('crop_id')->nullable()->constrained()->onDelete('set null');
            $table->string('crop_name');
            $table->year('year');
            $table->decimal(' hectares', 10, 2)->nullable();
            $table->decimal('expected_yield', 10, 2)->nullable();
            $table->decimal('actual_yield', 10, 2)->nullable();
            $table->string('yield_unit')->default('kg');
            $table->decimal('income', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('farmer_id');
            $table->index(['year', 'crop_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_history');
    }
};
