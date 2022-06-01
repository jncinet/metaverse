<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetaverseMachinesTable extends Migration
{
    /**
     * 矿机
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metaverse_machines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')
                ->comment('矿机名称');

            $table->unsignedInteger('count')
                ->default(0)
                ->comment('次数');
            $table->unsignedDecimal('price')
                ->default(0)
                ->comment('价格');
            $table->unsignedDecimal('power', 16, 8)
                ->default(0)
                ->comment('算力');

            // 下架、上架
            $table->boolean('status')->default(0)->comment('状态');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
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
        Schema::dropIfExists('metaverse_machines');
    }
}
