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
        Schema::table('livestock', function (Blueprint $table) {
            $table->string('tracking_id', 20)->unique()->nullable()->after('tag_number');
            $table->index('tracking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('livestock', function (Blueprint $table) {
            $table->dropIndex(['tracking_id']);
            $table->dropColumn('tracking_id');
        });
    }
};
