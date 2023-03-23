<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTaskTableAddCompletedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table
                ->dateTime('completed_at')
                ->nullable()
                ->after('priority');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('tasks', 'completed_at')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropColumn('completed_at');
            });
        }
    }
}
