<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetaverseRankingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metaverse_rankings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')
                ->index('user_index')
                ->comment('用户');
            // 自己购买的矿机总算力
            $table->unsignedDecimal('basic', 16, 8)
                ->default(0)
                ->comment('基础算力');
            // 直推下线提供总算力
            $table->unsignedDecimal('addition', 16, 8)
                ->default(0)
                ->comment('加成算力');
            // 加成算力 + 基础算力
            $table->unsignedDecimal('total', 16, 8)
                ->default(0)
                ->comment('总算力');
            // 大空间算力
            $table->unsignedDecimal('big_space', 16, 8)
                ->default(0)
                ->comment('大空间算力');
            // 小空间算力
            $table->unsignedDecimal('small_space', 16, 8)
                ->default(0)
                ->comment('小空间算力');
            // 推荐人
            $table->unsignedBigInteger('parent_id')
                ->index('parent_index')
                ->default(0)
                ->comment('推荐人');
            // 会员等级
            $table->unsignedBigInteger('metaverse_user_level_id')
                ->index('metaverse_user_level_index')
                ->default(0)
                ->comment('会员等级');
            // 团队等级
            $table->unsignedBigInteger('metaverse_team_level_id')
                ->index('metaverse_team_level_index')
                ->default(0)
                ->comment('团队等级');
            $table->string('api_username')
                ->nullable()
                ->index('api_username_index')
                ->comment('绑定接口会员名');
            $table->unsignedBigInteger('api_user_id')
                ->index('api_user_index')
                ->default(0)
                ->comment('绑定接口会员ID');
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
        Schema::dropIfExists('metaverse_rankings');
    }
}
