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
        Schema::create('consultings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_objective_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('expertise_manager_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('focus_area_manager_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamp('consulting_datetime')->nullable(); // Today’s date / meeting date
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
        Schema::dropIfExists('consultings');
    }
};
