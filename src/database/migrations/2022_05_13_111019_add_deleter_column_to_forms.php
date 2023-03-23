<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeleterColumnToForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('forms', function (Blueprint $table) {
            $table
                ->unsignedBigInteger('deleter_rio_id')
                ->nullable()
                ->after('supplier_neo_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('forms', 'deleter_rio_id')) {
            Schema::table('forms', function (Blueprint $table) {
                $table->dropColumn('deleter_rio_id');
            });
        }
    }
}
