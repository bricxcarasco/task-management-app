<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInFormBasicSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_basic_settings', function (Blueprint $table) {
            $table
                ->string('payment_terms_one', 255)
                ->nullable()
                ->after('business_number');
            $table
                ->string('payment_terms_two', 255)
                ->nullable()
                ->after('payment_terms_one');
            $table
                ->string('payment_terms_three', 255)
                ->nullable()
                ->after('payment_terms_two');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_basic_settings', function (Blueprint $table) {
            $table->dropColumn([
                'payment_terms_one',
                'payment_terms_two',
                'payment_terms_three',
            ]);
        });
    }
}
