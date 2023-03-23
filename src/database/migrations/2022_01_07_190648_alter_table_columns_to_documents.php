<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableColumnsToDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedBigInteger('rio_id')->nullable()->change();
            $table->unsignedBigInteger('neo_id')->nullable()->change();
            $table->unsignedBigInteger('directory')->comment('Folder (documents.id or null if root directory)')->nullable()->change();
            $table->string('mime_type')->nullable()->change();
            $table->unsignedBigInteger('file_bytes')->nullable()->change();

            $table->renameColumn('rio_id', 'owner_rio_id');
            $table->renameColumn('neo_id', 'owner_neo_id');
            $table->renameColumn('directory', 'directory_id');

            $table->foreign('owner_rio_id')->references('id')->on('rios')->onDelete('cascade');
            $table->foreign('owner_neo_id')->references('id')->on('neos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('documents', 'owner_rio_id')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->unsignedBigInteger('owner_rio_id')->change();
                $table->unsignedBigInteger('owner_neo_id')->change();
                $table->string('directory_id')->change();
                $table->string('mime_type')->change();
                $table->unsignedBigInteger('file_bytes')->change();

                $table->renameColumn('owner_rio_id', 'rio_id');
                $table->renameColumn('owner_neo_id', 'neo_id');
                $table->renameColumn('directory_id', 'directory');

                $table->foreign('rio_id')->references('id')->on('rios')->onDelete('cascade');
                $table->foreign('neo_id')->references('id')->on('neos')->onDelete('cascade');
            });
        }
    }
}
