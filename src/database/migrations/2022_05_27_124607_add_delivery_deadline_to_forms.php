<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveryDeadlineToForms extends Migration
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
                ->date('delivery_deadline')
                ->nullable()
                ->after('delivery_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('forms', 'delivery_deadline')) {
            Schema::table('forms', function (Blueprint $table) {
                $table->dropColumn('delivery_deadline');
            });
        }
    }
}
