<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkflowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workflows', function (Blueprint $table) {
            $table
                ->id()
                ->comment('id for Laravel');
            $table
                ->unsignedBigInteger('owner_neo_id')
                ->nullable();
            $table
                ->unsignedBigInteger('created_rio_id');
            $table
                ->string('workflow_title', 100);
            $table
                ->text('caption');
            $table
                ->text('attaches')
                ->comment("最大5ファイルまでjson形式で指定可能…セットするIDは 文章管理.ID");
            $table
                ->tinyInteger('status')
                ->comment("1: 申請中、2: 承認完了、3: 差戻中、4: 否認済、5: 申請取消");
            $table
                ->string('priority', 10)
                ->nullable()
                ->comment("(null): 設定なし、high: 高、mid: 中、low: 低");
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
        Schema::dropIfExists('workflows');
    }
}
