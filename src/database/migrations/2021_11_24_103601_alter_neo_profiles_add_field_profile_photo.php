<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNeoProfilesAddFieldProfilePhoto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('neo_profiles', function (Blueprint $table) {
            $table->string('profile_photo')->nullable()->collation('utf8_general_ci')->after('profile_video');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('neo_profiles', 'profile_photo')) {
            Schema::table('neo_profiles', function (Blueprint $table) {
                $table->dropColumn('profile_photo');
            });
        }
    }
}
