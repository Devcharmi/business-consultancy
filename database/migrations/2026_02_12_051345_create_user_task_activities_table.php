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
        Schema::create('user_task_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_task_id')->constrained()->onDelete('cascade');

            $table->string('activity_type');
            // created, status_changed, delayed, completed, reminder_sent etc

            $table->text('description')->nullable();
            // Human readable message

            $table->text('meta')->nullable();
            // JSON for extra data (old status, new status, delay days etc)

            $table->foreignId('performed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_task_activities');
    }
};
