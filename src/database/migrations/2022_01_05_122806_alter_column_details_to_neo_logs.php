<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnDetailsToNeoLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('neo_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('rio_id')->nullable()->change();
            $table->dropColumn('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('neo_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('rio_id')->change();
            $table->dateTime('deleted_at')->nullable()->comment('削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)');
        });
    }
}
