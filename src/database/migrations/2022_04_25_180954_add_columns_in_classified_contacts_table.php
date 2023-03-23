<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInClassifiedContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('classified_contacts', function (Blueprint $table) {
            $table
                ->unsignedBigInteger('selling_rio_id')
                ->nullable()
                ->comment("↓どちらかのみセット (it's either)")
                ->after('neo_id');
            $table
                ->unsignedBigInteger('selling_neo_id')
                ->nullable()
                ->comment("↑どちらかのみセット (it's either)")
                ->after('selling_rio_id');
            $table
                ->string('title', 100)
                ->after('selling_neo_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('classified_contacts', function (Blueprint $table) {
            $table->dropColumn([
                'selling_rio_id',
                'selling_neo_id',
                'title',
            ]);
        });
    }
}
