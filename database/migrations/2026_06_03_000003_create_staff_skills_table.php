<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->string('skill_name');
            $table->string('skill_category')->nullable();
            $table->enum('proficiency_level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('beginner');
            $table->string('certification')->nullable();
            $table->date('certification_expiry')->nullable();
            $table->date('acquired_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['staff_id', 'skill_name']);
            $table->unique(['staff_id', 'skill_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_skills');
    }
};
