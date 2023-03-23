<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterIdColumnPropertiesToDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_rio_id')->nullable()->change();
            $table->unsignedBigInteger('owner_neo_id')->nullable()->change();
            $table->unsignedBigInteger('directory_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_rio_id')->change();
            $table->unsignedBigInteger('owner_neo_id')->change();
            $table->text('directory_id')->change();
        });
    }
}
