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
        Schema::create('task_deliverables', function (Blueprint $table) {
            $table->id();

            $table->foreignId('task_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->text('deliverable');
            $table->date('expected_date')->nullable();

            $table->enum('status', ['pending', 'received'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_deliverables');
    }
};
