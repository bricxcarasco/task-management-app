<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIssuerColumnsToFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('forms', function (Blueprint $table) {
            // Drop columns
            $table->dropColumn([
                'payee_information',
                'payment_notes',
            ]);

            // Add new columns
            $table
                ->string('issuer_name', 255)
                ->nullable()
                ->after('remarks');
            $table
                ->string('issuer_zipcode', 255)
                ->nullable()
                ->after('issuer_name');
            $table
                ->string('issuer_address', 255)
                ->nullable()
                ->after('issuer_zipcode');
            $table
                ->string('issuer_tel', 255)
                ->nullable()
                ->after('issuer_address');
            $table
                ->string('issuer_fax', 255)
                ->nullable()
                ->after('issuer_tel');
            $table
                ->string('issuer_business_number', 255)
                ->nullable()
                ->after('issuer_fax');
            $table
                ->string('issuer_image', 255)
                ->nullable()
                ->after('issuer_business_number');
            $table
                ->text('issuer_payee_information')
                ->nullable()
                ->after('issuer_image');
            $table
                ->text('issuer_payment_notes')
                ->nullable()
                ->after('issuer_payee_information');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('forms', function (Blueprint $table) {
            $table
                ->text('payee_information')
                ->nullable()
                ->after('remarks');
            $table
                ->text('payment_notes')
                ->nullable()
                ->after('payee_information');

            $table->dropColumn([
                'issuer_name',
                'issuer_zipcode',
                'issuer_address',
                'issuer_tel',
                'issuer_fax',
                'issuer_business_number',
                'issuer_image',
                'issuer_payee_information',
                'issuer_payment_notes',
            ]);
        });
    }
}
