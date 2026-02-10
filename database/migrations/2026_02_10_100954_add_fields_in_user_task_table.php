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
        Schema::table('user_tasks', function (Blueprint $table) {

            // Make client nullable (important for lead tasks)
            $table->unsignedBigInteger('client_id')->nullable()->change();

            // Lead reference (only for lead tasks)
            $table->foreignId('lead_id')
                ->nullable()
                ->after('client_id')
                ->constrained('leads')
                ->nullOnDelete();

            // Identify lead or client
            $table->enum('entity_type', ['client', 'lead'])
                ->after('lead_id');

            // Identify task or meeting
            $table->enum('task_type', ['task', 'meeting'])
                ->after('entity_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_tasks', function (Blueprint $table) {

            // Drop FK first
            $table->dropForeign(['lead_id']);

            // Drop added columns
            $table->dropColumn(['lead_id', 'entity_type', 'task_type']);

            // Revert client_id to NOT NULL (only if you want strict rollback)
            $table->unsignedBigInteger('client_id')->nullable(false)->change();
        });
    }
};
