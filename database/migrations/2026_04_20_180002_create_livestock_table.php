<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('livestock_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('farm_id')->nullable()->constrained()->onDelete('set null');
            $table->string('tag_number')->nullable()->unique();
            $table->string('name')->nullable();
            $table->date('date_acquired')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('livestock')->onDelete('set null');
            $table->enum('status', ['healthy', 'sick', 'sold', 'dead'])->default('healthy');
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['livestock_type_id', 'status']);
            $table->index(['tag_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock');
    }
};
