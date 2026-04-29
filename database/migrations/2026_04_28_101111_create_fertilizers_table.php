<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fertilizers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fertilizer_type_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('quantity', 12, 2);
            $table->string('unit');
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->date('expiry_date')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['fertilizer_type_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fertilizers');
    }
};
