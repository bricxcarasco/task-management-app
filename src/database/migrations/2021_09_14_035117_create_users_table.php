<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id()->comment('id for Laravel');
            $table->unsignedBigInteger('rio_id');
            $table->string('username')->nullable()->collation('utf8_general_ci');
            $table->string('email')->unique()->collation('utf8_general_ci');
            $table->string('password', 64)->collation('utf8_general_ci');
            $table->string('remember_token', 100)->nullable()->comment('ログイン成功時、0リセット(0 reset upon successful login)')->collation('utf8_general_ci');
            $table->integer('login_failed')->default(0)->comment('0=アクティブ(active)、1=ロック(locked)');
            $table->integer('lock')->default(0);
            $table->dateTime('last_login')->nullable();
            $table->string('secret_question', 100)->nullable()->collation('utf8_general_ci');
            $table->string('secret_answer', 100)->nullable()->collation('utf8_general_ci');
            $table->integer('two_factor_authentication')->default(0)->comment('0=inactive, 1=email, 2=message');
            $table->dateTime('created_at')->nullable()->comment('登録日時(created datetime)')->useCurrent();
            $table->dateTime('updated_at')->nullable()->comment('更新日時(updated datetime)')->useCurrent();
            $table->dateTime('deleted_at')->nullable()->comment('削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)');
        
            $table->foreign('rio_id')->references('id')->on('rios')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
