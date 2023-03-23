<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpertAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expert_attributes', function (Blueprint $table) {
            $table->id()->comment('id for Laravel');
            $table->string('attribute_code', 50)->unique()->collation('utf8_general_ci');
            $table->string('attribute_name', 50)->collation('utf8_general_ci');
            $table->integer('is_searchable')->comment('0: 検索対象としない、1:検索対象とする');
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
        Schema::dropIfExists('expert_attributes');
    }
}
