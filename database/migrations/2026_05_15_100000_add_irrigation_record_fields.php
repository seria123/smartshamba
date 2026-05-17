<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('irrigation_logs', function (Blueprint $table) {
            $table->string('irrigation_method')->nullable()->after('event_type');
            $table->string('water_source')->nullable()->after('irrigation_method');
            $table->decimal('estimated_volume_liters', 10, 2)->nullable()->after('water_used_liters');
            $table->decimal('cost', 10, 2)->nullable()->after('estimated_volume_liters');
            $table->string('cost_type')->nullable()->after('cost');
        });
    }

    public function down(): void
    {
        Schema::table('irrigation_logs', function (Blueprint $table) {
            $table->dropColumn(['irrigation_method', 'water_source', 'estimated_volume_liters', 'cost', 'cost_type']);
        });
    }
};