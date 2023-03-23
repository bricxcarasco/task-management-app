<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInviteeIdColumnToElectronicContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('electronic_contracts', function (Blueprint $table) {
            $table
                ->string('invitee_id')
                ->collation('utf8_general_ci')
                ->after('dossier_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('electronic_contracts', 'invitee_id')) {
            Schema::table('electronic_contracts', function (Blueprint $table) {
                $table->dropColumn('invitee_id');
            });
        }
    }
}
