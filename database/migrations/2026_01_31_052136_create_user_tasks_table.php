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
        Schema::create('user_tasks', function (Blueprint $table) {
            $table->id();
            // 🔗 Relations
            $table->foreignId('staff_manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('client_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->text('task_name');
            $table->foreignId('priority_manager_id')->nullable()->constrained('priority_managers')->nullOnDelete();
            $table->date('task_start_date')->nullable();
            $table->date('task_end_date')->nullable();
            $table->date('task_due_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('status_manager_id')->nullable()->constrained('status_managers')->nullOnDelete();
            $table->text('description')->nullable();
            $table->timestamp('last_reminder_sent_at')->nullable();
            $table->string('source_type')->default('general')->nullable(); // commitment | deliverable
            $table->unsignedBigInteger('source_id')->nullable();

            $table->unique(['source_type', 'source_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_tasks');
    }
};
