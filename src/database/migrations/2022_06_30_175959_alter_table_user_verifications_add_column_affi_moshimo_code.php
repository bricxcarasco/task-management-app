<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableUserVerificationsAddColumnAffiMoshimoCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_verifications', function (Blueprint $table) {
            $table
                ->string('affi_moshimo_code', 255)
                ->nullable()
                ->after('expiration_datetime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('user_verifications', 'affi_moshimo_code')) {
            Schema::table('user_verifications', function (Blueprint $table) {
                $table->dropColumn('affi_moshimo_code');
            });
        }
    }
}
