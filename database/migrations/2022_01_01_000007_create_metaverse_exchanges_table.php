<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetaverseExchangesTable extends Migration
{
    /**
     * 兑出记录
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metaverse_exchanges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')
                ->index('user_index')
                ->comment('会员');
            $table->unsignedDecimal('amount', 16, 8)
                ->default(0)
                ->comment('转出金额');
            // 会员等级快照
            $table->unsignedBigInteger('metaverse_user_level_id')
                ->index('metaverse_user_level_index')
                ->comment('会员等级');
            // 会员燃料费比例快照
            $table->unsignedDecimal('rate')->default(0)
                ->comment('燃料费比例');
            // 实际收取的燃料费
            $table->unsignedDecimal('fees', 16, 8)->default(0)
                ->comment('燃料费金额');
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
        Schema::dropIfExists('metaverse_exchanges');
    }
}
