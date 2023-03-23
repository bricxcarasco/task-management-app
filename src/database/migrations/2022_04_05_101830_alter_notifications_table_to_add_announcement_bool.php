<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNotificationsTableToAddAnnouncementBool extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table
                ->integer('is_announcement')
                ->after('destination_url')
                ->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('notifications', 'is_announcement')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropColumn('is_announcement');
            });
        }
    }
}
