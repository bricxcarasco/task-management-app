<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_products', function (Blueprint $table) {
            $table
                ->id()
                ->comment('id for Laravel');
            $table
                ->unsignedBigInteger('form_id');
            $table
                ->unsignedBigInteger('rio_id')
                ->nullable()
                ->comment('↓どちらかのみセット');
            $table
                ->unsignedBigInteger('neo_id')
                ->nullable()
                ->comment('↑どちらかのみセット');
            $table
                ->unsignedBigInteger('created_rio_id');
            $table
                ->string('name', 255);
            $table
                ->integer('quantity')
                ->nullable();
            $table
                ->string('unit', 255)
                ->nullable();
            $table
                ->decimal('unit_price', 10, 2)
                ->nullable();
            $table
                ->integer('tax_distinction')
                ->nullable()
                ->comment('1:10%, 2:軽減8%, 3:対象外');
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
        Schema::dropIfExists('form_products');
    }
}
