<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::disableForeignKeyConstraints();

        // DB::table('user_task_activities')->truncate();
        // DB::table('users_expertise_manager')->truncate();
        // DB::table('user_tasks')->truncate();
        // DB::table('task_deliverables')->truncate();
        // DB::table('task_contents')->truncate();
        // DB::table('task_commitments')->truncate();
        // DB::table('task_attachments')->truncate();
        // DB::table('tasks')->truncate();
        // DB::table('lead_follow_ups')->truncate();
        // DB::table('leads')->truncate();
        // DB::table('client_objectives')->truncate();
        // DB::table('clients')->truncate();
        // DB::table('consultings')->truncate();
        // DB::table('focus_area_managers')->truncate();
        // DB::table('followups')->truncate();
        // DB::table('objective_managers')->truncate();

        // Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // No rollback needed for truncate
    }
};
