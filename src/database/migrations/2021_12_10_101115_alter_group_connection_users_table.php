<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterGroupConnectionUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add invite_message column to group_connections_users table
        Schema::table('group_connection_users', function (Blueprint $table) {
            $table
                ->string('invite_message')
                ->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_connection_users', function (Blueprint $table) {
            $table->dropColumn('invite_message');
        });
    }
}
