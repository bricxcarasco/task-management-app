<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id()
                ->comment('id for Laravel');

            $table->unsignedBigInteger('subscriber_id');

            $table->unsignedBigInteger('plan_service_id');

            $table->string('stripe_subscription_id', 100);

            $table->dateTime('start_date');

            $table->dateTime('end_date');

            $table->integer('quantity')
                ->default(1);

            $table->tinyInteger('status')
                ->comment('incomplete = 0, incomplete_expired = 1, trialing = 2, active = 3, past_due = 4, canceled or unpaid  = 5');

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
        Schema::dropIfExists('subscriptions');
    }
}
