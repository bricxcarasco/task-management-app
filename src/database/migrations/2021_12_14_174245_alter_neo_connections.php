<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNeoConnections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('neo_connections', function (Blueprint $table) {
            $table->dropForeign('neo_connnections_rio_id_foreign');
            $table->dropForeign('neo_connnections_neo_id_foreign');
            $table->unsignedBigInteger('connection_neo_id')->nullable()->change();
            $table->unsignedBigInteger('connection_rio_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
