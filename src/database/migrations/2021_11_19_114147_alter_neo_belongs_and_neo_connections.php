<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNeoBelongsAndNeoConnections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Fix neo_connections table
        Schema::rename('neo_connnections', 'neo_connections');

        // Alter neo_belongs table
        Schema::table('neo_belongs', function (Blueprint $table) {
            $table
                ->integer('is_display')
                ->default(0)
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
        Schema::table('neo_belongs', function (Blueprint $table) {
            $table->dropColumn('is_display');
        });
    }
}
