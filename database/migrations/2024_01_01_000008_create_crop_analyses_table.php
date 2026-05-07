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
        Schema::create('crop_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->string('diagnosis')->nullable();
            $table->text('description')->nullable();
            $table->string('severity')->nullable(); // low, medium, high
            $table->string('recommendation')->nullable();
            $table->json('detected_issues')->nullable(); // Array of detected issues
            $table->decimal('confidence_score', 5, 2)->nullable(); // AI confidence 0-100
            $table->enum('status', ['pending', 'analyzed', 'reviewed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crop_analyses');
    }
};
