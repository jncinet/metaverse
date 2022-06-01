<?php
/**
 * 奖励
 */

namespace Jncinet\Metaverse\Controllers\Wap;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jncinet\Metaverse\Models\MetaversePower;
use Jncinet\Metaverse\Models\MetaverseReward;
use Jncinet\Metaverse\Support;
use Illuminate\Http\JsonResponse;
use Exception;

class RewardController extends Controller
{
    public function __construct()
    {
        App::setLocale('en');
    }

    // 矿机列表
    public function index(Request $request)
    {
        // 默认查询用户所有奖励
        $where = [
            ['user_id', Auth::id()],
        ];

        // 根据矿机订单查询奖励
        $metaverse_power_id = (int)$request->query('metaverse_power_id', 0);
        if ($metaverse_power_id > 0) {
            $where[] = ['type', 0];
            $where[] = ['metaverse_power_id', $metaverse_power_id];
        }
        $rewards = MetaverseReward::where($where)
            ->orderBy('id', 'desc')
            ->simplePaginate();

        return view('metaverse::reward.index', compact('rewards'));
    }

    /**
     * 领取当日奖励
     *
     * @return JsonResponse
     */
    public function update(): JsonResponse
    {
        try {
            // 验证任务是否完成了
            if (Support::verifyTaskIsCompleted(Auth::id())) {

                // 每日只能完成一次
                if (MetaverseReward::whereDate('created_at', now()->toDateString())->exists()) {
                    throw new Exception(trans('metaverse::reward.once_day'));
                }

                $totalAmount = DB::transaction(function () {
                    // 奖励总金额
                    $total_amount = 0;

                    $miner_output = config('metaverse.miner_output', 0);
                    // 判断是否关闭了奖励
                    if ($miner_output > 0) {
                        // 取得所有当日未获取奖励的有效矿机
                        $powers = MetaversePower::whereDoesntHave('metaverse_rewards', function (Builder $query) {
                            $query->whereDate('created_at', now()->toDateString());
                        })
                            ->where('user_id', Auth::id())
                            ->where('remaining_count', '>', 0)
                            ->get();

                        // 获取推荐人
                        $recommender_id = Support::userRecommenderId(Auth::id());
                        // 直推奖励比例
                        $miner_recommender_reward = config('metaverse.miner_recommender_reward');
                        // 百分比转小数
                        $miner_recommender_reward = bcdiv($miner_recommender_reward, 100, 4);

                        foreach ($powers as $power) {
                            // 算力产出的金额
                            $amount = bcmul($miner_output, $power->total_power, 8);

                            // 累计奖励金额
                            $total_amount = bcadd($amount, $total_amount, 8);

                            // 创建奖励记录
                            $rewardModel = MetaverseReward::create([
                                'type' => 0,
                                'amount' => $amount,
                                'user_id' => $power->user_id,
                                'metaverse_power_id' => $power->id,
                                'metaverse_main_id' => 0,
                                'metaverse_reward_id' => 0,
                                'status' => 1,
                            ]);

                            if (!$rewardModel) {
                                throw new Exception(trans('metaverse::reward.create_failed'));
                            }

                            // 更新剩余次数
                            $power->remaining_count -= 1;

                            if (!$power->save()) {
                                throw new Exception(trans('metaverse::reward.create_failed'));
                            }

                            // 发放奖励
                            if (!Support::returnReward($power->user_id, $amount)) {
                                throw new Exception(trans('metaverse::reward.create_failed'));
                            }

                            // 当矿机使用次数用完时，释放质押和算力
                            if ($power->remaining_count <= 0) {
                                // 释放质押
                                Support::returnStacking($power->user_id, $power->total_price);
                                // 释放算力
                                Support::releasePower($power->user_id, $power->total_power);
                            }

                            // 验证推荐人是否存在
                            if ($recommender_id && $miner_recommender_reward > 0) {
                                // 直推奖励金额
                                $floatAmount = bcmul($miner_recommender_reward, $amount, 8);
                                if ($floatAmount > 0) {
                                    // 直推奖励记录
                                    MetaverseReward::create([
                                        'type' => 2,
                                        'amount' => $floatAmount,
                                        'user_id' => $recommender_id,
                                        'metaverse_power_id' => 0,
                                        'metaverse_main_id' => 0,
                                        'metaverse_reward_id' => $rewardModel->id,
                                        'status' => 1,
                                    ]);

                                    // 直推奖励发放
                                    Support::returnReward($recommender_id, $floatAmount);
                                }
                            }
                        }
                    } else {
                        throw new Exception(trans('metaverse::reward.reward_closed'));
                    }

                    return $total_amount;
                });

                return Support::success(
                    ['amount' => number_format($totalAmount, 4)],
                    trans('metaverse::reward.task_completed')
                );
            }
            throw new Exception(trans('metaverse::reward.task_not_completed'));
        } catch (Exception $exception) {
            return Support::error('', $exception->getMessage(), $exception->getCode());
        }
    }
}
