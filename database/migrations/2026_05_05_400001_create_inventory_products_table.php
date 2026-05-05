<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_products', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('inventory_product_categories')->restrictOnDelete();
            $table->string('name');
            $table->string('code');
            $table->string('sku')->nullable();
            $table->string('product_type');
            $table->string('unit_of_measure');
            $table->string('brand')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('active_ingredient')->nullable();
            $table->boolean('tracks_batch')->default(false);
            $table->boolean('tracks_expiry')->default(false);
            $table->decimal('reorder_level', 12, 2)->default(0);
            $table->decimal('default_unit_cost', 12, 2)->default(0);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->unique(['organization_id', 'code']);
            $table->index(['organization_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_products');
    }
};
