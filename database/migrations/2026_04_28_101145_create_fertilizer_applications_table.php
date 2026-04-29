<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fertilizer_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fertilizer_id')->constrained()->onDelete('cascade');
            $table->foreignId('field_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity_used', 12, 2);
            $table->string('unit');
            $table->date('application_date');
            $table->string('growth_stage')->nullable(); // e.g., vegetative, flowering
            $table->string('application_method')->nullable(); // e.g., broadcast, banding
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['fertilizer_id', 'field_id']);
            $table->index(['application_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fertilizer_applications');
    }
};
