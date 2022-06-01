<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetaverseTeamLevelsTable extends Migration
{
    /**
     * 团队等级
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metaverse_team_levels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 55)
                ->comment('等级名称');
            // 大空间算力
            $table->unsignedDecimal('big_power', 16, 8)
                ->default(0)
                ->comment('大空间算力值');
            // 小空间算力
            $table->unsignedDecimal('small_power', 16, 8)
                ->default(0)
                ->comment('小空间算力值');
            $table->decimal('main_reward_rate')->default(0)
                ->comment('矿池分红比例');
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
        Schema::dropIfExists('metaverse_team_levels');
    }
}
