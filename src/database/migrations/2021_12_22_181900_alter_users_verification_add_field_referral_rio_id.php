<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersVerificationAddFieldReferralRioId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_verifications', function (Blueprint $table) {
            $table->unsignedBigInteger('referral_rio_id')->nullable()->after('token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('user_verifications', 'referral_rio_id')) {
            Schema::table('user_verifications', function (Blueprint $table) {
                $table->dropColumn('referral_rio_id');
            });
        }
    }
}
