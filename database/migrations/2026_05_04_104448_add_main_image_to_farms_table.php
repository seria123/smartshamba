<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farms', function (Blueprint $table) {
            $table->foreignId('main_image_id')->nullable()->constrained('farm_images')->onDelete('set null')->after('storage_facilities');
        });
    }

    public function down(): void
    {
        Schema::table('farms', function (Blueprint $table) {
            $table->dropForeign(['main_image_id']);
            $table->dropColumn('main_image_id');
        });
    }
};
