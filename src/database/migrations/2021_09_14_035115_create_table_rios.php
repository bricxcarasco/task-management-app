<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableRios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rios', function (Blueprint $table) {
            $table->id()->comment('id for Laravel');
            $table->string('family_name')->collation('utf8_general_ci');
            $table->string('first_name')->collation('utf8_general_ci');
            $table->string('family_kana')->collation('utf8_general_ci');
            $table->string('first_kana')->collation('utf8_general_ci');
            $table->date('birth_date');
            $table->string('gender', 100)->collation('utf8_general_ci');
            $table->string('tel', 100)->collation('utf8_general_ci')->comment('fill in user\'s living country if user not living in Japan');
            $table->dateTime('created_at')->nullable()->comment('登録日時(created datetime)')->useCurrent();
            $table->dateTime('updated_at')->nullable()->comment('更新日時(updated datetime)')->useCurrent();
            $table->dateTime('deleted_at')->nullable()->comment('削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)');
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
