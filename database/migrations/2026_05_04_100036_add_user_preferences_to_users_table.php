<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('preferred_language', 5)->nullable()->after('email')->default('en');
            $table->json('notification_preferences')->nullable()->after('preferred_language');
            $table->boolean('weather_alerts')->default(false)->after('notification_preferences');
            $table->boolean('ai_recommendations')->default(true)->after('weather_alerts');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['preferred_language', 'notification_preferences', 'weather_alerts', 'ai_recommendations']);
        });
    }
};
