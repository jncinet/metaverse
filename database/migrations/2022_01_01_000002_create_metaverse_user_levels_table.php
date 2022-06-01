<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetaverseUserLevelsTable extends Migration
{
    /**
     * 会员等级
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metaverse_user_levels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 55)
                ->comment('等级名称');
            // 等级条件
            $table->unsignedDecimal('power', 16, 8)
                ->default(0)
                ->comment('算力值');
            $table->unsignedSmallInteger('subordinates_number')
                ->default(0)
                ->comment('直推认证用户数量');
            // 燃料费比例
            $table->unsignedDecimal('gas_fee_rate')
                ->default(0)
                ->comment('燃料费比例');
            $table->unsignedTinyInteger('sort')
                ->default(0)
                ->comment('排序');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('metaverse_user_levels');
    }
}
