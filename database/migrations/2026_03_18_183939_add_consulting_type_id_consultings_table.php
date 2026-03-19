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
            $table->foreignId('consulting_type_id')->nullable()->constrained('consulting_types')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultings', function (Blueprint $table) {
            $table->dropColumn('consulting_type_id');
        });
    }
};
