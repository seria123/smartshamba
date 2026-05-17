<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('harvests', function (Blueprint $table) {
            $table->string('harvest_number')->nullable()->after('harvest_date');
            $table->decimal('grade_1_quantity', 12, 2)->default(0)->after('quality_percentage');
            $table->decimal('grade_2_quantity', 12, 2)->default(0)->after('grade_1_quantity');
            $table->decimal('rejects_quantity', 12, 2)->default(0)->after('grade_2_quantity');
            $table->enum('destination', ['store', 'sold_directly', 'processing'])->default('store')->after('loss_percentage');
            $table->string('buyer_reference')->nullable()->after('destination');
            
            $table->index(['crop_cycle_id', 'harvest_date']);
        });
    }

    public function down(): void
    {
        Schema::table('harvests', function (Blueprint $table) {
            $table->dropColumn([
                'harvest_number',
                'grade_1_quantity',
                'grade_2_quantity',
                'rejects_quantity',
                'destination',
                'buyer_reference',
            ]);
        });
    }
};