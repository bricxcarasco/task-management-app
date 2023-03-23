<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('rio_id')
                ->nullable()
                ->comment('↓どちらかのみセット');

            $table->unsignedBigInteger('neo_id')
                ->nullable()
                ->comment('↑どちらかのみセット');

            $table->string('stripe_customer_id', 100);

            $table->unsignedBigInteger('plan_id');

            $table->string('stripe_subscription_id', 100);

            $table->string('stripe_client_secret');

            $table->tinyInteger('status')
                ->comment('incomplete = 0, incomplete_expired = 1, trialing = 2, active = 3, past_due = 4, canceled or unpaid  = 5');

            $table->dateTime('start_date');

            $table->dateTime('end_date');

            $table->tinyInteger('payment_method')
                ->comment('1 - CC/Stripe, 2 - Bank Transfer');

            $table->integer('total_price');

            $table->text('data')
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
        Schema::dropIfExists('subscribers');
    }
}
