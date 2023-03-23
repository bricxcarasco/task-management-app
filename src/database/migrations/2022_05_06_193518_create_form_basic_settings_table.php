<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormBasicSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_basic_settings', function (Blueprint $table) {
            $table
                ->id()
                ->comment('id for Laravel');
            $table
                ->unsignedBigInteger('rio_id')
                ->nullable()
                ->comment('↓どちらかのみセット');
            $table
                ->unsignedBigInteger('neo_id')
                ->nullable()
                ->comment('↑どちらかのみセット');
            $table
                ->unsignedBigInteger('created_rio_id');
            $table
                ->string('name', 255);
            $table
                ->string('zipcode', 255)
                ->nullable();
            $table
                ->string('address', 255)
                ->nullable();
            $table
                ->string('tel', 255)
                ->nullable();
            $table
                ->string('fax', 255)
                ->nullable();
            $table
                ->string('business_number', 255)
                ->nullable();
            $table
                ->string('image', 255)
                ->nullable();
            $table
                ->dateTime('created_at')
                ->nullable()
                ->comment('登録日時(created datetime)')
                ->useCurrent();
            $table
                ->dateTime('updated_at')
                ->nullable()
                ->comment('更新日時(updated datetime)')
                ->useCurrent();
            $table
                ->dateTime('deleted_at')
                ->nullable()
                ->comment('削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_basic_settings');
    }
}
