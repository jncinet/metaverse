<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetaverseMainsTable extends Migration
{
    /**
     * 矿池 {手续费(银河燃料)收入记录、分红记录}
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metaverse_mains', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')
                ->default(0)
                ->index('user_index')
                ->comment('会员');
            // 0燃料收入、1分红给不同等级的领导、2区块链系统销毁、3区域节点奖励
            $table->unsignedTinyInteger('type')
                ->default(0)
                ->index('type_index')
                ->comment('类型');
            // 收入关联兑出记录
            $table->unsignedBigInteger('metaverse_exchange_id')
                ->default(0)
                ->index('metaverse_exchange_index')
                ->comment('关联兑出记录');
            // 收到或支付的金额
            $table->decimal('amount')->default(0)
                ->comment('收入或支出燃料值');
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
        Schema::dropIfExists('metaverse_mains');
    }
}
