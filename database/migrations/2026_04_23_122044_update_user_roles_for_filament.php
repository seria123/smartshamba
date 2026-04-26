<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change role from enum to string for flexibility
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 50)->change();
        });

        // Rename existing 'admin' users to 'manager'
        DB::table('users')->where('role', 'admin')->update(['role' => 'manager']);

        // Optionally create a super admin user if none exists
        // You can change this email to your admin email
        $superAdminExists = DB::table('users')->where('role', 'admin')->exists();
        if (! $superAdminExists) {
            // You can manually create an admin user via tinker or seeder later
            // DB::table('users')->insert([...]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'user'])->change();
        });

        // Convert 'manager' back to 'admin' on rollback
        DB::table('users')->where('role', 'manager')->update(['role' => 'admin']);
    }
};
