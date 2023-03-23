<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReceiptColumnsToForms extends Migration
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
                ->string('refer_receipt_no', 255)
                ->nullable()
                ->after('issuer_payment_notes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('forms', 'refer_receipt_no')) {
            Schema::table('forms', function (Blueprint $table) {
                $table->dropColumn('refer_receipt_no');
            });
        }
    }
}
