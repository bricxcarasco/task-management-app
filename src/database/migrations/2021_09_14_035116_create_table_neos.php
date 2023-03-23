<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableNeos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('neos', function (Blueprint $table) {
            $table->id()->comment('id for Laravel');
            $table->string('organization_name')->collation('utf8_general_ci');
            $table->string('organization_kana')->collation('utf8_general_ci');
            $table->string('organization_type', 100)->collation('utf8_general_ci');
            $table->date('establishment_date');
            $table->string('email')->collation('utf8_general_ci');
            $table->string('tel', 100)->collation('utf8_general_ci');
            $table->text('site_url')->collation('utf8_general_ci');
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
        Schema::dropIfExists('neos');
    }
}
