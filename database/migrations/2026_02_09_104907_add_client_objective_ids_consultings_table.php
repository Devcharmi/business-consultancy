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
        Schema::table('consultings', function (Blueprint $table) {
            $table->foreignId('client_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->nullOnDelete();


            $table->foreignId('objective_manager_id')
                ->nullable()
                ->after('client_id')
                ->constrained()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultings', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['objective_manager_id']);

            $table->dropColumn(['client_id', 'objective_manager_id']);
        });
    }
};
