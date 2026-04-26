<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crop_seasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crop_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('season_period');
            $table->date('planting_start_date');
            $table->date('planting_end_date');
            $table->date('expected_harvest_start');
            $table->date('expected_harvest_end');
            $table->text('notes')->nullable();
            $table->boolean('is_optimal')->default(true);
            $table->timestamps();

            $table->index('crop_id');
            $table->index('season_period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_seasons');
    }
};
