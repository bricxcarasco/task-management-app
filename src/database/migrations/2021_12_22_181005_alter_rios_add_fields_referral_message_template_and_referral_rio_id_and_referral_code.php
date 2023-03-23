<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRiosAddFieldsReferralMessageTemplateAndReferralRioIdAndReferralCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rios', function (Blueprint $table) {
            $table->string('referral_message_template', 500)->nullable()->collation('utf8_general_ci')->after('tel');
            $table->unsignedBigInteger('referral_rio_id')->nullable()->after('tel');
            $table->string('referral_code', 20)->collation('utf8_general_ci')->after('tel');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('rios', 'referral_message_template')) {
            Schema::table('rios', function (Blueprint $table) {
                $table->dropColumn('referral_message_template');
                $table->dropColumn('referral_rio_id');
                $table->dropColumn('referral_code');
            });
        }
    }
}
