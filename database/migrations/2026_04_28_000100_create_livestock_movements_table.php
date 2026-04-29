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
        Schema::create('livestock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('livestock_id')->constrained('livestock')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('movement_type'); // transfer, sale, treatment, inspection, birth, purchase
            $table->foreignId('from_farm_id')->nullable()->constrained('farms')->onDelete('set null');
            $table->foreignId('to_farm_id')->nullable()->constrained('farms')->onDelete('set null');
            $table->foreignId('from_field_id')->nullable()->constrained('fields')->onDelete('set null');
            $table->foreignId('to_field_id')->nullable()->constrained('fields')->onDelete('set null');
            $table->dateTime('movement_date');
            $table->text('reason')->nullable();
            $table->json('metadata')->nullable(); // weight, condition_score, etc.
            $table->timestamps();

            $table->index(['livestock_id', 'movement_date']);
            $table->index(['from_farm_id', 'to_farm_id']);
            $table->index('movement_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livestock_movements');
    }
};
