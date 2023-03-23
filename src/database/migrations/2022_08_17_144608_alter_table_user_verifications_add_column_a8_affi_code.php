<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableUserVerificationsAddColumnA8AffiCode extends Migration
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
                ->string('affi_a8_code', 255)
                ->nullable()
                ->after('affi_moshimo_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('user_verifications', 'affi_a8_code')) {
            Schema::table('user_verifications', function (Blueprint $table) {
                $table->dropColumn('affi_a8_code');
            });
        }
    }
}
