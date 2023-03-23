<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDocumentsMakeStoragePathNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->text('storage_path')->collation('utf8_general_ci')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('documents', 'storage_path')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->text('storage_path')->collation('utf8_general_ci')->change();
            });
        }
    }
}
