<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
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
                ->integer('chat_type')
                ->comment('1:つながりチャット、2:つながりグループチャット、3:NEOチームチャット、4:NEOメッセージ配信');
            $table
                ->string('room_name', 100);
            $table
                ->string('room_icon', 200);
            $table
                ->string('room_caption', 1000);
            $table
                ->string('status', 50)
                ->comment('active, archive');
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
        Schema::dropIfExists('chats');
    }
}
