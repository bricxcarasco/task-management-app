<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRioExpertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rio_experts', function (Blueprint $table) {
            $table->id()->comment('id for Laravel');
            $table->unsignedBigInteger('rio_id');
            $table->string('attribute_code', 50)->collation('utf8_general_ci')->comment('※ここではIDでなくコードを使用する。');
            $table->integer('sort')->comment('データの並び順を設定');
            $table->unsignedBigInteger('business_category_id')->nullable()->comment('attribute_codeが"experience"の場合に使用');
            $table->string('content', 100)->collation('utf8_general_ci');
            $table->string('additional', 50)->collation('utf8_general_ci')->nullable();
            $table->text('information')->collation('utf8_general_ci')->nullable()->comment('json形式で記録');
            $table->dateTime('created_at')->nullable()->comment('登録日時(created datetime)')->useCurrent();
            $table->dateTime('updated_at')->nullable()->comment('更新日時(updated datetime)')->useCurrent();
            $table->dateTime('deleted_at')->nullable()->comment('削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)');

            $table->foreign('rio_id')->references('id')->on('rios')->onDelete('cascade');
            $table->foreign('attribute_code')->references('attribute_code')->on('expert_attributes')->onDelete('cascade');
            $table->foreign('business_category_id')->references('id')->on('business_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rio_experts');
    }
}
