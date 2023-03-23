<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNeoGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add group_name column to neo_groups table
        Schema::table('neo_groups', function (Blueprint $table) {
            $table
                ->string('group_name')
                ->after('rio_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('neo_groups', function (Blueprint $table) {
            $table->dropColumn('group_name');
        });
    }
}
