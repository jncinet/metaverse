<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetaversePowersTable extends Migration
{
    /**
     * 订单 我购买的矿机
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metaverse_powers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')
                ->index('user_index')
                ->comment('会员');
            $table->unsignedBigInteger('metaverse_machine_id')
                ->index('metaverse_machine_index')
                ->comment('矿机');
            $table->unsignedSmallInteger('quantity')
                ->default(1)
                ->comment('购买台数');
            // 矿机属性快照
            $table->unsignedSmallInteger('count')->comment('有效次数');
            $table->unsignedDecimal('price')->comment('购机单价');
            $table->unsignedDecimal('power', 16, 8)->comment('算力值');
            // 每天计算后，减少次数
            $table->unsignedSmallInteger('remaining_count')->comment('剩余次数');
            // 小计
            $table->unsignedDecimal('total_price')->comment('总购买金额');
            $table->unsignedDecimal('total_power', 16, 8)->comment('总算力');

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
        Schema::dropIfExists('metaverse_powers');
    }
}
