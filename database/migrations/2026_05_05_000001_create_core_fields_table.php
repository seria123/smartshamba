<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fields', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->decimal('area', 12, 2)->nullable();
            $table->string('area_unit')->default('acres');
            $table->string('status')->default('active');
            $table->timestamps();

            $table->unique(['farm_id', 'name']);
            $table->index(['organization_id', 'farm_id', 'site_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};
