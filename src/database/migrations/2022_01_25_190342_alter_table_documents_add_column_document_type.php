<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDocumentsAddColumnDocumentType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->tinyInteger('document_type')->comment('1:Folder（フォルダ）,2:File（ファイル）3:Attachement(添付ファイル)')->after('directory_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('documents', 'document_type')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropColumn('document_type');
            });
        }
    }
}
