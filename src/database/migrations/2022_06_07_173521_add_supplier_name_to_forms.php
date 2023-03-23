<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupplierNameToForms extends Migration
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
                ->string('supplier_name', 255)
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
        if (Schema::hasColumn('forms', 'supplier_name')) {
            Schema::table('forms', function (Blueprint $table) {
                $table->dropColumn('supplier_name');
            });
        }
    }
}
