<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lead_follow_ups', function (Blueprint $table) {
            $table->enum('status', ['pending', 'completed'])
                ->default('pending')
                ->after('next_follow_up_at');

            $table->timestamp('completed_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('lead_follow_ups', function (Blueprint $table) {
            $table->dropColumn(['status', 'completed_at']);
        });
    }
};
