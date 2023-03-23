<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChatIdToGroupConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_connections', function (Blueprint $table) {
            $table
                ->unsignedBigInteger('chat_id')
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
        Schema::table('group_connections', function (Blueprint $table) {
            $table->dropColumn('chat_id');
        });
    }
}
