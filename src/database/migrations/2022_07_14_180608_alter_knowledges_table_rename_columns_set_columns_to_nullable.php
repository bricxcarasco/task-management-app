<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterKnowledgesTableRenameColumnsSetColumnsToNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('knowledges', function (Blueprint $table) {
            $table->text('contents')->nullable()->change();
            $table->text('urls')->nullable()->change();
            $table->integer('is_draft')->nullable()->change();
            $table->dropColumn('directory');
            $table->unsignedBigInteger('directory_id')->nullable()->after('created_rio_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('knowledges', 'directory_id')) {
            Schema::table('knowledges', function (Blueprint $table) {
                $table->text('contents')->nullable(false)->change();
                $table->text('urls')->nullable(false)->change();
                $table->integer('is_draft')->nullable(false)->change();
                $table->dropColumn('directory_id');
                $table->string('directory')->nullable(false)->after('created_rio_id');
            });
        }
    }
}
