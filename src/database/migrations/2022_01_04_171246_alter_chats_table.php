<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chats', function (Blueprint $table) {
            $table
                ->string('room_icon', 200)
                ->nullable()
                ->change();
            $table
                ->string('room_caption', 1000)
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
        Schema::table('chats', function (Blueprint $table) {
            $table
                ->string('room_icon', 200)
                ->change();
            $table
                ->string('room_caption', 1000)
                ->change();
        });
    }
}
