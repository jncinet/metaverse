<?php

namespace Jncinet\Metaverse\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Jncinet\Metaverse\Models\MetaversePower;
use Jncinet\Metaverse\Models\MetaverseReward;
use Jncinet\Metaverse\Support;
use Exception;

class Mining implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $power;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(MetaversePower $power)
    {
        $this->power = $power;
    }

    /**
     * 根据算力产出积分
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $miner_output = config('metaverse.miner_output', 0);
        if ($this->power->remaining_count > 0 && $miner_output > 0) {
            // 创建奖励记录
            if (MetaverseReward::create([
                'type' => 0,
                'amount' => bcmul($miner_output, $this->power->total_power, 8),
                'user_id' => $this->power->user_id,
                'metaverse_power_id' => $this->power->id,
                'metaverse_main_id' => 0,
                'metaverse_reward_id' => 0,
                'status' => 1,
            ])) {
                // 减少矿机使用次数
                $this->power->remaining_count -= 1;
                $this->power->save();
            }
        }

        // 创建下次任务
        if ($this->power->remaining_count <= 0) {
            // 释放质押
            Support::returnStacking($this->power->user_id, $this->power->total_price);
            // 释放算力
            Support::releasePower($this->power->user_id, $this->power->total_power);
        }
    }
}
