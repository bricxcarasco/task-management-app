<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSiteUrlPropertiesToNeos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('neos', function (Blueprint $table) {
            $table->text('site_url')->collation('utf8_general_ci')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('neos', function (Blueprint $table) {
            $table->text('site_url')->collation('utf8_general_ci')->change();
        });
    }
}
