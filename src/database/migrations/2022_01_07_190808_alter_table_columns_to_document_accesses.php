<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableColumnsToDocumentAccesses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_accesses', function (Blueprint $table) {
            $table->unsignedBigInteger('neo_id')->nullable()->change();
            $table->unsignedBigInteger('rio_id')->nullable()->change();
            $table->unsignedBigInteger('neo_group_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('document_accesses', function (Blueprint $table) {
            $table->unsignedBigInteger('neo_id')->change();
            $table->unsignedBigInteger('rio_id')->change();
            $table->unsignedBigInteger('neo_group_id')->change();
        });
    }
}
