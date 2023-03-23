<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_verifications', function (Blueprint $table) {
            $table->id()->comment('id for Laravel');
            $table->string('email')->collation('utf8_general_ci');
            $table->string('token', 100)->collation('utf8_general_ci')->comment('ランダムなハッシュ値を取得しメールに付与。一致する場合に会員登録画面を開く');
            $table->dateTime('expiration_datetime')->comment('会員登録画面が開かれる時点でこのテーブルのうち、登録期限日時を超えたレコードは物理削除を行う');
            $table->dateTime('created_at')->nullable()->comment('登録日時(created datetime)')->useCurrent();
            $table->dateTime('updated_at')->nullable()->comment('更新日時(updated datetime)')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_verifications');
    }
}
