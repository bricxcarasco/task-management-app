<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableTasksAddColumnRemarks extends Migration
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
                ->string('remarks', 255)
                ->nullable()
                ->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('tasks', 'remarks')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropColumn('remarks');
            });
        }
    }
}
