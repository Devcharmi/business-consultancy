<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('task_commitments', function (Blueprint $table) {
            $table->date('commitment_date')->nullable()->after('task_id');
        });
    }

   public function down()
    {
        Schema::table('task_commitments', function (Blueprint $table) {
            // 3️⃣ Drop column
            $table->dropColumn('commitment_date');
        });
    }
};
