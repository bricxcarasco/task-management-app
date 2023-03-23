<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNeoProfilesAddFieldsSelfIntroduceAndProfileVideo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('neo_profiles', function (Blueprint $table) {
            $table->text('self_introduce')->nullable()->collation('utf8_general_ci')->after('building');
            $table->text('profile_video')->nullable()->collation('utf8_general_ci')->after('self_introduce');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('neo_profiles', 'self_introduce')) {
            Schema::table('neo_profiles', function (Blueprint $table) {
                $table->dropColumn('self_introduce');
            });
        }
    }
}
