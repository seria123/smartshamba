<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_rules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->nullable()->constrained('farms')->nullOnDelete();
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('source_module');
            $table->string('signal_type');
            $table->string('severity')->default('medium');
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'farm_id', 'code']);
        });

        Schema::create('farm_notifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('farm_id')->nullable()->constrained('farms')->nullOnDelete();
            $table->foreignId('notification_rule_id')->nullable()->constrained('notification_rules')->nullOnDelete();
            $table->foreignId('recipient_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('recipient_role_id')->nullable()->constrained('roles')->nullOnDelete();
            $table->string('title');
            $table->text('message');
            $table->string('severity')->default('medium');
            $table->string('status')->default('unread');
            $table->string('source_module')->nullable();
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('signal_type')->nullable();
            $table->string('source_label')->nullable();
            $table->string('action_url')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('dismissed_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'farm_id', 'status']);
            $table->index(['source_module', 'source_type', 'source_id', 'signal_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_notifications');
        Schema::dropIfExists('notification_rules');
    }
};
