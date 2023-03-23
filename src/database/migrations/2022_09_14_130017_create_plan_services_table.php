<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_services', function (Blueprint $table) {
            $table->id()
                ->comment('id for Laravel');

            $table->unsignedBigInteger('plan_id');

            $table->unsignedBigInteger('service_id');

            $table->tinyInteger('type')
                ->comment('1: Plan Inclusion, 2: Plan Additional/Option');

            $table->string('stripe_price_id', 100)
                ->nullable();

            $table->integer('value')
                ->default(1);

            $table->string('unit', 100)
                ->nullable();

            $table->integer('price')
                ->nullable();

            $table->text('description')
                ->nullable();

            $table->dateTime('created_at')
                ->nullable()
                ->comment('登録日時(created datetime)')
                ->useCurrent();

            $table->dateTime('updated_at')
                ->nullable()
                ->comment('更新日時(updated datetime)')
                ->useCurrent();

            $table->dateTime('deleted_at')
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
        Schema::dropIfExists('plan_services');
    }
}
