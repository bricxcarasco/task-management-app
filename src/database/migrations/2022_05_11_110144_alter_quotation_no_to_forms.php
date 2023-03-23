<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterQuotationNoToForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn('quotation_no');
            $table
                ->string('form_no', 100)
                ->after('supplier_neo_id');
            $table
                ->text('remarks')
                ->nullable()
                ->after('receipt_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('forms', 'form_no')) {
            Schema::table('forms', function (Blueprint $table) {
                $table->dropColumn('form_no');
                $table
                    ->string('quotation_no', 100)
                    ->after('created_rio_id');
                $table->dropColumn('remarks');
            });
        }
    }
}
