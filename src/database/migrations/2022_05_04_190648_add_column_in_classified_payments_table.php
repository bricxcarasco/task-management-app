<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInClassifiedPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('classified_payments', function (Blueprint $table) {
            $table
                ->string('stripe_payment_intent_id', 100)
                ->nullable()
                ->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('classified_payments', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_payment_intent_id',
            ]);
        });
    }
}
