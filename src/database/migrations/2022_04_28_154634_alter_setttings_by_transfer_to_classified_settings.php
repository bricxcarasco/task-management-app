<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSetttingsByTransferToClassifiedSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('classified_settings', function (Blueprint $table) {
            $table->renameColumn('setttings_by_transfer', 'settings_by_transfer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('classified_settings', function (Blueprint $table) {
            //
        });

        if (Schema::hasColumn('classified_settings', 'settings_by_transfer')) {
            Schema::table('classified_settings', function (Blueprint $table) {
                $table->renameColumn('settings_by_transfer', 'setttings_by_transfer');
            });
        }
    }
}
