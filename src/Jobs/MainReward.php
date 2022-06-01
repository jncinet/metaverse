<?php

namespace Jncinet\Metaverse\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Jncinet\Metaverse\Models\MetaverseMain;
use Jncinet\Metaverse\Models\MetaverseRanking;
use Jncinet\Metaverse\Models\MetaverseReward;
use Jncinet\Metaverse\Models\MetaverseTeamLevel;
use Exception;

class MainReward implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * 计算发放分红
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $type = config('metaverse.miner_main_reward_amount');
        if ($type > 0) {
            $total = config('metaverse.miner_main_reward_amount');
        } elseif ($type < 0) {
            // 取前一天的所有矿池收入
            $total = MetaverseMain::whereDate('created_at', now()->subDays()->toDateString())
                ->where('type', 0)
                ->orderBy('id', 'desc')
                ->sum('amount');
        } else {
            // 关闭矿池奖励
            return null;
        }

        // 如果当日有运行记录取消任务
        if (MetaverseMain::whereDate('created_at', now()->toDateString())->where('type', '>', 0)->exists()) {
            return null;
        }

        // 团队奖励
        $team_amount = bcmul($total, config('metaverse.miner_main_reward_team_rate'), 10);
        $team_amount = bcdiv($team_amount, 100, 8);

        // 节点奖励
        $item_amount = bcmul($total, config('metaverse.miner_main_reward_item_rate'), 10);
        $item_amount = bcdiv($item_amount, 100, 8);

        // 要销毁的金额
        $destroy_amount = bcmul($total, config('metaverse.miner_main_reward_destroy_rate'), 10);
        $destroy_amount = bcdiv($destroy_amount, 100, 8);

        // 记录领导分红金额
        $team_row = $this->createTeamRow($team_amount);

        if ($team_row) {
            $metaverse_main_id = $team_row->id;
            // 按等级发放分红
            $teamModels = MetaverseTeamLevel::all();

            foreach ($teamModels as $teamModel) {
                if ($teamModel->main_reward_rate <= 0) {
                    continue;
                }

                $level_amount = bcmul($teamModel->main_reward_rate, $team_amount, 10);
                $level_amount = bcdiv($level_amount, 100, 12);
                if ($level_amount <= 0) {
                    continue;
                }

                // 级别领导数量
                $level_count = MetaverseRanking::where('metaverse_team_level_id', $teamModel->id)->count();
                if ($level_count <= 0) {
                    continue;
                }

                // 平分金额
                $average_amount = bcdiv($level_amount, $level_count, 8);
                if ($average_amount <= 0) {
                    continue;
                }

                // 分块发奖励
                MetaverseRanking::select('id', 'user_id')
                    ->where('metaverse_team_level_id', $teamModel->id)
                    ->orderBy('id', 'desc')
                    ->chunk(66, function ($items) use ($average_amount, $metaverse_main_id) {
                        foreach ($items as $item) {
                            MetaverseReward::create([
                                'type' => 1,
                                'amount' => $average_amount,
                                'user_id' => $item->user_id,
                                'metaverse_power_id' => 0,
                                'metaverse_main_id' => $metaverse_main_id,
                                'metaverse_reward_id' => 0,
                                // 分红时任务未完成，直接获取
                                'status' => 1,
                            ]);
                        }
                    });
            }
        }

        $this->createItemRow($item_amount);

        $this->createDestroyRow($destroy_amount);
    }

    // 创建领导分红行记录
    private function createTeamRow($amount)
    {
        if ($amount <= 0) return null;
        return MetaverseMain::create([
            'user_id' => 0,
            'type' => 1,
            'metaverse_exchange_id' => 0,
            'amount' => -$amount,
        ]);
    }

    // 创建区域节点行记录
    private function createItemRow($amount)
    {
        if ($amount <= 0) return null;
        return MetaverseMain::create([
            'user_id' => 0,
            'type' => 3,
            'metaverse_exchange_id' => 0,
            'amount' => -$amount,
        ]);
    }

    // 创建销毁行记录
    private function createDestroyRow($amount)
    {
        if ($amount <= 0) return null;
        return MetaverseMain::create([
            'user_id' => 0,
            'type' => 2,
            'metaverse_exchange_id' => 0,
            'amount' => -$amount,
        ]);
    }
}
