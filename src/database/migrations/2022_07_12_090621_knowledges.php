<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Knowledges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('knowledges', function (Blueprint $table) {
            $table
                ->id()
                ->comment('id for Laravel');
            $table
                ->unsignedBigInteger('owner_rio_id')
                ->nullable()
                ->comment('↓どちらかのみセット');
            $table
                ->unsignedBigInteger('owner_neo_id')
                ->nullable()
                ->comment('↑どちらかのみセット');
            $table
                ->unsignedBigInteger('created_rio_id');
            $table
                ->string('directory', 1000);
            $table
                ->string('task_title', 255);
            $table
                ->text('contents')
                ->comment('WYSIWYGエディタによるHTML形式で保存');
            $table
                ->text('urls')
                ->comment('json形式で5つまで登録可能');
            $table
                ->integer('is_draft')
                ->comment('0: 公開、1: 下書き');
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
        Schema::dropIfExists('knowledges');
    }
}
