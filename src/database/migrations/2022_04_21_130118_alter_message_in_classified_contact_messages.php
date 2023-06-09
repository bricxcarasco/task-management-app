<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMessageInClassifiedContactMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('classified_contact_messages', function (Blueprint $table) {
            $table
                ->string('message', 2500)
                ->nullable()
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('classified_contact_messages', function (Blueprint $table) {
            $table
                ->string('message', 2500)
                ->change();
        });
    }
}
