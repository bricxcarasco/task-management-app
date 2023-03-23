<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableRioProfilesAddColumnMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rio_connections', function (Blueprint $table) {
            $table
                ->string('message', 255)
                ->nullable()
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
        if (Schema::hasColumn('rio_connections', 'message')) {
            Schema::table('rio_connections', function (Blueprint $table) {
                $table->dropColumn('message');
            });
        }
    }
}
