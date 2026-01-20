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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('objective_manager_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('expertise_manager_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title'); // Task Name / Meeting
            $table->longText('content')->nullable(); // Task / Meeting Content

            $table->date('task_start_date')->nullable(); // Today’s date / meeting date
            $table->date('task_due_date')->nullable(); // Today’s date / meeting date

            $table->enum('type', ['task', 'meeting'])->default('meeting');

            $table->foreignId('status_manager_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
