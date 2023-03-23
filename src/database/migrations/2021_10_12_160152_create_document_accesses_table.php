<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentAccessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_accesses', function (Blueprint $table) {
            $table->id()->comment('id for Laravel');
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('neo_id');
            $table->unsignedBigInteger('rio_id');
            $table->unsignedBigInteger('neo_group_id');
            $table->dateTime('created_at')->nullable()->comment('登録日時(created datetime)')->useCurrent();
            $table->dateTime('updated_at')->nullable()->comment('更新日時(updated datetime)')->useCurrent();
            $table->dateTime('deleted_at')->nullable()->comment('削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)');

            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
            $table->foreign('neo_id')->references('id')->on('neos')->onDelete('cascade');
            $table->foreign('rio_id')->references('id')->on('rios')->onDelete('cascade');
            $table->foreign('neo_group_id')->references('id')->on('neo_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_accesses');
    }
}
