<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users_expertise_manager', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('expertise_manager_id')
                ->constrained('expertise_managers')
                ->cascadeOnDelete();

            // Optional but useful
            $table->boolean('is_primary')->default(false);

            $table->timestamps();

            // Prevent duplicate assignment
            $table->unique(['user_id', 'expertise_manager_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users_expertise_manager');
    }
};
