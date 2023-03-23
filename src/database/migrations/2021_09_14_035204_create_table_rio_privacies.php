<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableRioPrivacies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rio_privacies', function (Blueprint $table) {
            $table->id()->comment('id for Laravel');
            $table->unsignedBigInteger('rio_id');
            $table->integer('accept_conncections');
            $table->integer('accept_conncections_by_neo');
            $table->dateTime('created_at')->nullable()->comment('登録日時(created datetime)')->useCurrent();
            $table->dateTime('updated_at')->nullable()->comment('更新日時(updated datetime)')->useCurrent();
            $table->dateTime('deleted_at')->nullable()->comment('削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)');
        
            $table->foreign('rio_id')->references('id')->on('rios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rio_privacies');
    }
}
