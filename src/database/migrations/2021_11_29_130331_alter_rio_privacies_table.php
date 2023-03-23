<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRioPrivaciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rio_privacies', function (Blueprint $table) {
            $table->renameColumn('accept_conncections', 'accept_connections');
            $table->renameColumn('accept_conncections_by_neo', 'accept_connections_by_neo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rio_privacies', function (Blueprint $table) {
            $table->renameColumn('accept_connections', 'accept_conncections');
            $table->renameColumn('accept_connections_by_neo', 'accept_conncections_by_neo');
        });
    }
}
