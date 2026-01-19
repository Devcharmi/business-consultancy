<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_objectives', function (Blueprint $table) {
            $table->id();

            // 🔗 Relations
            $table->foreignId('client_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('objective_manager_id')
                ->constrained()
                ->cascadeOnDelete();

            // 🏷 Status
            $table->enum('status', ['0', '1'])
                ->default('1');

            // 📝 Optional info
            $table->text('note')->nullable();

            // 👤 Audit
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // 🔒 Prevent duplicates
            $table->unique(['client_id', 'objective_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_objectives');
    }
};
