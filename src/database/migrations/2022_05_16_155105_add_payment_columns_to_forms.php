<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentColumnsToForms extends Migration
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
                ->text('payee_information')
                ->nullable()
                ->after('remarks');
            $table
                ->text('payment_notes')
                ->nullable()
                ->after('remarks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('forms', 'payment_notes')) {
            Schema::table('forms', function (Blueprint $table) {
                $table->dropColumn('payment_notes');
                $table->dropColumn('payee_information');
            });
        }
    }
}
