<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableNeoProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('neo_profiles', function (Blueprint $table) {
            $table->id()->comment('id for Laravel');
            $table->unsignedBigInteger('neo_id');
            $table->string('nationality')->nullable()->collation('utf8_general_ci');
            $table->string('prefecture')->nullable()->collation('utf8_general_ci');
            $table->string('city')->nullable()->collation('utf8_general_ci');
            $table->string('address')->nullable()->collation('utf8_general_ci');
            $table->string('building')->nullable()->collation('utf8_general_ci');
            $table->dateTime('created_at')->nullable()->comment('登録日時(created datetime)')->useCurrent();
            $table->dateTime('updated_at')->nullable()->comment('更新日時(updated datetime)')->useCurrent();
            $table->dateTime('deleted_at')->nullable()->comment('削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)');
        
            $table->foreign('neo_id')->references('id')->on('neos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('neo_profiles');
    }
}
