<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterGroupConnectionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Fix group_connection_users table
        Schema::rename('group_connectiion_users', 'group_connection_users');

        // Add group_name column to group_connections table
        Schema::table('group_connections', function (Blueprint $table) {
            $table
                ->string('group_name')
                ->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_connections', function (Blueprint $table) {
            $table->dropColumn('group_name');
        });
    }
}
