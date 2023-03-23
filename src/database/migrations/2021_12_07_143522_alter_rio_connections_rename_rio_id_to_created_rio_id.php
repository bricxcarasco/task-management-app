<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRioConnectionsRenameRioIdToCreatedRioId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rio_connections', function (Blueprint $table) {
            $table->renameColumn('rio_id', 'created_rio_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rio_connections', function (Blueprint $table) {
            $table->renameColumn('created_rio_id', 'rio_id');
        });
    }
}
