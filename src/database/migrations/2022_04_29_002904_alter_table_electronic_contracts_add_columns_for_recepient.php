<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableElectronicContractsAddColumnsForRecepient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('electronic_contracts', function (Blueprint $table) {
            $table->unsignedBigInteger('recipient_rio_id')->nullable()->after('dossier_id');
            $table->unsignedBigInteger('recipient_neo_id')->nullable()->after('recipient_rio_id');
            $table->string('recipient_email')->collation('utf8_general_ci')->after('recipient_neo_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('electronic_contracts', 'recipient_rio_id')) {
            Schema::table('electronic_contracts', function (Blueprint $table) {
                $table->dropColumn('recipient_rio_id');
            });
        }

        if (Schema::hasColumn('electronic_contracts', 'recipient_neo_id')) {
            Schema::table('electronic_contracts', function (Blueprint $table) {
                $table->dropColumn('recipient_neo_id');
            });
        }

        if (Schema::hasColumn('electronic_contracts', 'recipient_email')) {
            Schema::table('electronic_contracts', function (Blueprint $table) {
                $table->dropColumn('recipient_email');
            });
        }
    }
}
