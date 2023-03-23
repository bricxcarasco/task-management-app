<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNeoProfilesAddFieldOverseasSupport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('neo_profiles', function (Blueprint $table) {
            $table->integer('overseas_support')
                ->comment('0:無、1:有')
                ->default(1)
                ->after('profile_photo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('neo_profiles', 'overseas_support')) {
            Schema::table('neo_profiles', function (Blueprint $table) {
                $table->dropColumn('overseas_support');
            });
        }
    }
}
