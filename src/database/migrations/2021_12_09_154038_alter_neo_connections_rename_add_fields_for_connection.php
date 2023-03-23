<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNeoConnectionsRenameAddFieldsForConnection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('neo_connections', function (Blueprint $table) {
            $table->renameColumn('rio_id', 'connection_rio_id');
            $table->unsignedBigInteger('connection_neo_id')->after('rio_id');

            $table->foreign('connection_neo_id')->references('id')->on('neos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('neo_connections', 'connection_rio_id')) {
            Schema::table('neo_connections', function (Blueprint $table) {
                $table->renameColumn('connection_rio_id', 'rio_id');
                $table->dropColumn('connection_neo_id');
            });
        }
    }
}
