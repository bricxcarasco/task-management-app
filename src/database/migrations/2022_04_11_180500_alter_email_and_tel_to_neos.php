<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEmailAndTelToNeos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('neos', function (Blueprint $table) {
            $table->string('email')->collation('utf8_general_ci')->nullable()->change();
            $table->string('tel', 100)->collation('utf8_general_ci')->nullable()->change();
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
            $table->string('email')->collation('utf8_general_ci')->change();
            $table->string('tel', 100)->collation('utf8_general_ci')->change();
        });
    }
}
