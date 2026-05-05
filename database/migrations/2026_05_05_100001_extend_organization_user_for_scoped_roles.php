<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organization_user', function (Blueprint $table): void {
            $table->foreignId('role_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->foreignId('farm_id')->nullable()->after('role_key')->constrained()->nullOnDelete();
            $table->string('status')->default('active')->after('farm_id');
        });
    }

    public function down(): void
    {
        Schema::table('organization_user', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('role_id');
            $table->dropConstrainedForeignId('farm_id');
            $table->dropColumn('status');
        });
    }
};
