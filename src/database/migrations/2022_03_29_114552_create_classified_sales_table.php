<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassifiedSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classified_sales', function (Blueprint $table) {
            $table
                ->id()
                ->comment('id for Laravel');
            $table
                ->unsignedBigInteger('selling_rio_id')
                ->nullable()
                ->comment("↓どちらかのみセット (it's either)");
            $table
                ->unsignedBigInteger('selling_neo_id')
                ->nullable()
                ->comment("↑どちらかのみセット (it's either)");
            $table
                ->unsignedBigInteger('created_rio_id');
            $table
                ->string('sale_category', 50);
            $table
                ->string('title', 100);
            $table
                ->string('detail', 1000);
            $table
                ->text('images')
                ->nullable()
                ->comment('最大5ファイルまでjson形式で指定可能… （文書管理サービスは使わない）');
            $table
                ->decimal('price', 10, 2)
                ->nullable()
                ->comment('NULL：「個別見積」　必要になるまでは小数点以下を使用しない');
            $table
                ->integer('is_accept')
                ->comment('0: 受付中止(Closed)、1: 受付中 (Open)');
            $table
                ->integer('is_public')
                ->comment('0: 非公開(Private)、1:公開(Private)');
            $table
                ->dateTime('created_at')
                ->nullable()
                ->comment('登録日時(created datetime)')
                ->useCurrent();
            $table
                ->dateTime('updated_at')
                ->nullable()
                ->comment('更新日時(updated datetime)')
                ->useCurrent();
            $table
                ->dateTime('deleted_at')
                ->nullable()
                ->comment('削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('classified_sales');
    }
}
