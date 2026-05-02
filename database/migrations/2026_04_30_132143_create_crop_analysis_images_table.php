<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crop_analysis_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crop_analysis_id')
                  ->constrained('crop_analyses')
                  ->onDelete('cascade');
            $table->string('image_path');
            $table->integer('order')->default(0);
            $table->string('label')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crop_analysis_images');
    }
};
