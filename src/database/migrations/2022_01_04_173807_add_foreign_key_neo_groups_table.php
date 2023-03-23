<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyNeoGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add chat_id column to neo_groups table
        Schema::table('neo_groups', function (Blueprint $table) {
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
        Schema::table('neo_groups', function (Blueprint $table) {
            $table->dropColumn('chat_id');
        });
    }
}
