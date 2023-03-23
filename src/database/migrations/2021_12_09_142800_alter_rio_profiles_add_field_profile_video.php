<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRioProfilesAddFieldProfileVideo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rio_profiles', function (Blueprint $table) {
            $table->string('profile_video')->nullable()->collation('utf8_general_ci')->after('home_address_city');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('rio_profiles', 'self_introduce')) {
            Schema::table('rio_profiles', function (Blueprint $table) {
                $table->dropColumn('profile_video');
            });
        }
    }
}
