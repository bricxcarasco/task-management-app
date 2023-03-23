<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRioProfilesDropFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rio_profiles', function (Blueprint $table) {
            $table->dropColumn(['profession', 'educational_background', 'neo_affiliates', 'qualifications', 'skills', 'awards', 'product_service_information']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rio_profiles', function (Blueprint $table) {
            $table->text('profession')->nullable()->collation('utf8_general_ci')->comment('Can be registered 10 contents by json format');
            $table->text('educational_background')->nullable()->collation('utf8_general_ci')->comment('Can be registered 20 contents from NEO(in public) which the user participates by json format');
            $table->text('neo_affiliates')->nullable()->collation('utf8_general_ci')->comment('Can be registered 10 contents by json format');
            $table->text('qualifications')->nullable()->collation('utf8_general_ci')->comment('Can be registered 10 contents by json format');
            $table->text('skills')->nullable()->collation('utf8_general_ci')->comment('Can be registered 10 contents by json format');
            $table->text('awards')->nullable()->collation('utf8_general_ci')->comment('');
            $table->text('product_service_information')->nullable()->collation('utf8_general_ci')->comment('Can be registered 10 contents by json format(content: 商品・サービス名, 写真, HERO内リンクURL)');
        });
    }
}
