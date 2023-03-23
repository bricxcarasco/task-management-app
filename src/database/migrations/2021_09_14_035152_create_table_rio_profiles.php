<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableRioProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rio_profiles', function (Blueprint $table) {
            $table->id()->comment('id for Laravel');
            $table->unsignedBigInteger('rio_id');
            $table->string('profile_photo')->nullable()->collation('utf8_general_ci');
            $table->text('self_introduce')->nullable()->collation('utf8_general_ci');
            $table->integer('business_use')->default(0)->comment('0-off, 1-on');
            $table->string('present_address_nationality')->nullable()->collation('utf8_general_ci');
            $table->string('present_address_prefecture')->nullable()->collation('utf8_general_ci');
            $table->string('present_address_city')->nullable()->collation('utf8_general_ci');
            $table->string('present_address')->nullable()->collation('utf8_general_ci');
            $table->string('present_address_building')->nullable()->collation('utf8_general_ci');
            $table->string('home_address_nationality')->nullable()->collation('utf8_general_ci');
            $table->string('home_address_prefecture')->nullable()->collation('utf8_general_ci');
            $table->string('home_address_city')->nullable()->collation('utf8_general_ci');
            $table->text('profession')->nullable()->collation('utf8_general_ci')->comment('Can be registered 10 contents by json format');
            $table->text('educational_background')->nullable()->collation('utf8_general_ci')->comment('Can be registered 20 contents from NEO(in public) which the user participates by json format');
            $table->text('neo_affiliates')->nullable()->collation('utf8_general_ci')->comment('Can be registered 10 contents by json format');
            $table->text('qualifications')->nullable()->collation('utf8_general_ci')->comment('Can be registered 10 contents by json format');
            $table->text('skills')->nullable()->collation('utf8_general_ci')->comment('Can be registered 10 contents by json format');
            $table->text('awards')->nullable()->collation('utf8_general_ci')->comment('');
            $table->text('product_service_information')->nullable()->collation('utf8_general_ci')->comment('Can be registered 10 contents by json format
            (content: 商品・サービス名, 写真, HERO内リンクURL)');
            $table->dateTime('created_at')->nullable()->comment('登録日時(created datetime)')->useCurrent();
            $table->dateTime('updated_at')->nullable()->comment('更新日時(updated datetime)')->useCurrent();
            $table->dateTime('deleted_at')->nullable()->comment('削除日時(deleted datetime):null=レコード有効、not null=削除扱い(null=Valid record, not null=Delete treatment)');

            $table->foreign('rio_id')->references('id')->on('rios')->onDelete('cascade');
            ;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rio_profiles');
    }
}
