<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterKnowledgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('knowledges', function (Blueprint $table) {
            $table->tinyInteger('type')->comment('1:Folder（フォルダ）,2:Article（記事)')->after('directory');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('knowledges', 'type')) {
            Schema::table('knowledges', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
}
