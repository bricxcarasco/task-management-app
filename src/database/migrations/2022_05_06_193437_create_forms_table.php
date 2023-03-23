<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forms', function (Blueprint $table) {
            $table
                ->id()
                ->comment('id for Laravel');
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
                ->string('quotation_no', 100);
            $table
                ->unsignedBigInteger('supplier_rio_id')
                ->nullable()
                ->comment('↓どちらかのみセット');
            $table
                ->unsignedBigInteger('supplier_neo_id')
                ->nullable()
                ->comment('↑どちらかのみセット');
            $table
                ->string('title', 255);
            $table
                ->integer('type')
                ->default(1)
                ->comment('1:quotation, 2:purchase history, 3:delivery slip, 4:invoice, 5:receipt');
            $table
                ->decimal('price', 10, 2);
            $table
                ->string('zipcode', 255)
                ->nullable();
            $table
                ->string('address', 255)
                ->nullable();
            $table
                ->string('subject', 255);
            $table
                ->string('delivery_address', 255);
            $table
                ->text('payment_terms');
            $table
                ->date('delivery_date')
                ->nullable();
            $table
                ->date('payment_date')
                ->nullable();
            $table
                ->date('issue_date')
                ->nullable();
            $table
                ->date('expiration_date')
                ->nullable();
            $table
                ->date('receipt_date')
                ->nullable();
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
        Schema::dropIfExists('forms');
    }
}
