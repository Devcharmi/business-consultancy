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
            $table->date('consulting_date')->nullable()->after('focus_area_manager_id');
            $table->time('start_time')->nullable()->after('consulting_date');
            $table->time('end_time')->nullable()->after('start_time');

            $table->dropColumn('consulting_datetime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultings', function (Blueprint $table) {
            $table->dateTime('consulting_datetime')->nullable();

            $table->dropColumn(['consulting_date', 'start_time', 'end_time']);
        });
    }
};
