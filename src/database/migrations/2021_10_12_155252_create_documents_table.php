<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id()->comment('id for Laravel');
            $table->unsignedBigInteger('rio_id');
            $table->unsignedBigInteger('neo_id');
            $table->text('directory')->collation('utf8_general_ci');
            $table->string('document_name')->collation('utf8_general_ci');
            $table->string('mime_type', 100)->collation('utf8_general_ci');
            $table->unsignedBigInteger('storage_type_id')->comment('1:HERO、2:GoogleDrive、3:Dropbox...');
            $table->text('storage_path')->collation('utf8_general_ci');
            $table->bigInteger('file_bytes');
            $table->dateTime('created_at')->nullable()->comment('登録日時(created datetime)')->useCurrent();
            $table->dateTime('updated_at')->nullable()->comment('更新日時(updated datetime)')->useCurrent();
            $table->dateTime('deleted_at')->nullable()->comment('削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)');
        
            $table->foreign('rio_id')->references('id')->on('rios')->onDelete('cascade');
            $table->foreign('neo_id')->references('id')->on('neos')->onDelete('cascade');
            $table->foreign('storage_type_id')->references('id')->on('storage_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents');
    }
}
