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
        Schema::create('livestock_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('livestock_id')->nullable()->constrained('livestock')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('image_path');
            $table->string('diagnosis')->nullable();
            $table->text('description')->nullable();
            $table->enum('severity', ['low', 'medium', 'high'])->nullable();
            $table->text('recommendation')->nullable();
            $table->json('detected_issues')->nullable();
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->enum('status', ['pending', 'analyzed', 'reviewed'])->default('pending');
            $table->timestamps();

            // Indexes for better query performance
            $table->index('livestock_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livestock_analyses');
    }
};
