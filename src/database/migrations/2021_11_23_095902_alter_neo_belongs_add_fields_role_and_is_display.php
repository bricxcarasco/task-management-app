<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Neo\RoleType;

class AlterNeoBelongsAddFieldsRoleAndIsDisplay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('neo_belongs', function (Blueprint $table) {
            $table->integer('role')->default(RoleType::OWNER)->comment('1:オーナー（owner）、2:管理者（administrator）、3:メンバー（member）')->after('rio_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('neo_belongs', 'role')) {
            Schema::table('neo_belongs', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
}
