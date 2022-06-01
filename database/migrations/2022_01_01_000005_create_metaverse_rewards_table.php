<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetaverseRewardsTable extends Migration
{
    /**
     * 分红日志
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metaverse_rewards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')
                ->index('user_index')
                ->comment('会员');
            // 收入类型: 0矿机产出、1团队分红、2直推奖励
            $table->unsignedTinyInteger('type')
                ->index('type_index')
                ->comment('收入类型');
            $table->unsignedDecimal('amount', 16, 8)
                ->comment('收入金额');
            // 矿机产出：关联我的产出矿机
            $table->unsignedBigInteger('metaverse_power_id')
                ->default(0)
                ->comment('关联我的产出矿机');
            // 团队分红：关联矿池变化记录
            $table->unsignedBigInteger('metaverse_main_id')
                ->default(0)
                ->comment('关联矿池变化记录');
            // 直推奖励：关联本表矿机产出记录
            $table->unsignedBigInteger('metaverse_reward_id')
                ->default(0)
                ->comment('关联矿机产出记录');
            // 完成任务后才能获取 => 0未领取，1已到账
            $table->boolean('status')
                ->default(0)
                ->comment('状态');
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
        Schema::dropIfExists('metaverse_rewards');
    }
}
