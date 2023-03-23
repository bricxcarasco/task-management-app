<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterClassifiedSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('classified_settings', function (Blueprint $table) {
            $table
                ->text('settings_by_card')
                ->nullable()
                ->comment('（Stripe Connectの詳細確認中のためWIP）')
                ->change();
            $table
                ->text('setttings_by_transfer')
                ->nullable()
                ->comment('JSON形式で3件まで振込先口座を登録可能')
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
        Schema::table('classified_settings', function (Blueprint $table) {
            $table
                ->text('settings_by_card')
                ->comment('（Stripe Connectの詳細確認中のためWIP）')
                ->change();
            $table
                ->text('setttings_by_transfer')
                ->comment('JSON形式で3件まで振込先口座を登録可能')
                ->change();
        });
    }
}
