<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassifiedPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classified_payments', function (Blueprint $table) {
            $table
                ->id()
                ->comment('id for Laravel');
            $table
                ->unsignedBigInteger('classified_sale_id');
            $table
                ->unsignedBigInteger('rio_id')
                ->nullable()
                ->comment('↓どちらかのみセット');
            $table
                ->unsignedBigInteger('neo_id')
                ->nullable()
                ->comment('↑どちらかのみセット');
            $table
                ->string('access_key', 128)
                ->comment('ハッシュ値またはランダム文字列を保持');
            $table
                ->decimal('price', 10, 2)
                ->nullable()
                ->comment('必要になるまで（日本で運用する限り）は小数点以下を使用しない');
            $table
                ->string('payment_method', 10);
            $table
                ->integer('status')
                ->comment('0: 決済待ち、1: 決済完了(自動反映)、2: 決済完了(手動反映)');
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
        Schema::dropIfExists('classified_payments');
    }
}
