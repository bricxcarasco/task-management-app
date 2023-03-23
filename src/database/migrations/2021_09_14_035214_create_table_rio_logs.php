<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableRioLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rio_logs', function (Blueprint $table) {
            $table->id()->comment('id for Laravel');
            $table->unsignedBigInteger('rio_id');
            $table->dateTime('logged_at')->comment('current time in Web server(not DB server)');
            $table->integer('log_type');
            $table->text('log_detail')->comment('by json format')->collation('utf8_general_ci');
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
        Schema::dropIfExists('rio_logs');
    }
}
