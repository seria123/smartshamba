<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('livestock_diseases', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('livestock_id')
                ->constrained('livestock')
                ->cascadeOnDelete();
            $table->foreignId('disease_id')->nullable()->constrained()->nullOnDelete();

            // Disease info
            $table->string('name');
            $table->string('species');
            $table->string('cause');
            $table->text('symptoms');
            $table->text('transmission')->nullable();
            $table->text('prevention')->nullable();
            $table->text('treatment')->nullable();

            // Tracking fields (YOU WERE MISSING THESE)
            $table->string('status')->default('active');
            $table->date('diagnosed_date')->nullable();
            $table->date('treated_date')->nullable();
            $table->foreignId('treated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->decimal('mortality_rate', 5, 2)->nullable();
            $table->enum('severity', ['low', 'medium', 'high'])->default('medium');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_diseases');
    }
};
