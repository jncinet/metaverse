<?php

namespace Jncinet\Metaverse;

use App\Models\Spread;
use App\Models\Task;
use App\Models\TaskRewardLog;
use App\Models\User;
use App\Repositories\AccountRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Jncinet\Metaverse\Models\MetaverseRanking;
use Jncinet\Metaverse\Models\MetaverseTeamLevel;
use Jncinet\Metaverse\Models\MetaverseUserLevel;

class Support
{
    /**
     * 质押支付
     *
     * event:
     * 'metaverse_pledge' => '矿场质押',
     * 'metaverse_pledge_release' => '矿场释放质押',
     * 'metaverse_reward' => '矿场收益',
     * 'metaverse_exchange' => '兑出',
     *
     * @param int $user_id
     * @param float $amount
     * @return bool
     */
    public static function payBalance(int $user_id, float $amount): bool
    {
        $repository = new AccountRepository();
        return $repository->updateIntegral($user_id, -$amount,
            'metaverse_pledge', ['model' => 'metaverse']);
    }

    /**
     * 兑出至其它平台
     *
     * @param int $user_id
     * @param float $amount
     * @return bool
     */
    public static function exchangeBalance(int $user_id, float $amount): bool
    {


        $repository = new AccountRepository();
        return $repository->updateIntegral($user_id, -$amount,
            'metaverse_exchange', ['model' => 'metaverse']);
    }

    /**
     * 发放奖励
     *
     * @param int $user_id
     * @param float $amount
     * @return bool
     */
    public static function returnReward(int $user_id, float $amount): bool
    {
        $repository = new AccountRepository();
        return $repository->updateIntegral($user_id, $amount,
            'metaverse_reward', ['model' => 'metaverse']);
    }

    /**
     * 释放质押
     *
     * @param int $user_id
     * @param float $amount
     * @return bool
     */
    public static function returnStacking(int $user_id, float $amount): bool
    {
        $repository = new AccountRepository();
        return $repository->updateIntegral($user_id, $amount,
            'metaverse_pledge_release', ['model' => 'metaverse']);
    }

    /**
     * 推荐人数
     *
     * @param int $user_id
     * @return int
     */
    public static function getUserSubordinatesNumber(int $user_id): int
    {
        $ids = config('metaverse.miner_authentications');
        if (empty($ids)) {
            $count = Spread::where('parent_id', $user_id)->count();
        } else {
            $ids = explode(',', $ids);
            $count = Spread::whereHas('authentication_logs', function (Builder $query) use ($ids) {
                $query->where('status', 2)
                    ->whereIn('authentication_id', $ids);
            }, '=', count($ids))
                ->where('parent_id', 1)
                ->count();
        }

        return $count;
    }

    /**
     * 获取会员等级
     *
     * @param int $user_id
     * @param float $power
     * @return mixed
     */
    public static function getUserLevel(int $user_id, float $power)
    {
        $where = [
            // 算力条件
            ['power', '<=', $power],
        ];
        // 直推认证人数
        if (!empty(config('metaverse.miner_authentications'))) {
            $where[] =  ['subordinates_number', '<=', self::getUserSubordinatesNumber($user_id)];
        }
        return MetaverseUserLevel::where($where)
            ->orderBy('power', 'desc')
            ->orderBy('sort', 'desc')
            ->first();
    }

    /**
     * 获取团队等级
     *
     * @param float $big_space
     * @param float $small_space
     * @return mixed
     */
    public static function getTeamLevel(float $big_space, float $small_space)
    {
        return MetaverseTeamLevel::where('big_power', '<=', $big_space)
            ->where('small_power', '<=', $small_space)
            ->orderBy('big_power', 'desc')
            ->orderBy('small_power', 'desc')
            ->orderBy('sort', 'desc')
            ->first();
    }

    /**
     * 大空间
     *
     * @param int $user_id
     * @param bool $onlyModel
     * @return mixed
     */
    public static function getUserBigSpace(int $user_id, bool $onlyModel = false)
    {
        $model = MetaverseRanking::where('parent_id', $user_id)
            ->where('addition', '>', 0)
            ->orderBy('addition', 'desc')
            ->limit(2)
            ->get();
        if ($onlyModel) return $model;
        return $model
            ? ['power' => $model->sum('addition'), 'ids' => $model->pluck('id')->toArray()]
            : ['power' => 0, 'ids' => []];
    }

    /**
     * 小空间
     *
     * @param int $user_id
     * @param array $big_space_ids
     * @param bool $paginate
     * @return mixed
     */
    public static function getUserSmallSpace(int $user_id, array $big_space_ids = [], bool $paginate = false)
    {
        if ($paginate) {
            // 小空间线列表分页
            return MetaverseRanking::where('parent_id', $user_id)
                ->whereNotIn('id', $big_space_ids)
                ->where('addition', '>', 0)
                ->orderBy('addition', 'desc')
                ->simplePaginate();
        } else {
            // 小空间算力
            return MetaverseRanking::where('parent_id', $user_id)
                ->whereNotIn('id', $big_space_ids)
                ->sum('addition');
        }
    }

    /**
     * 读取加成算力或来源
     *
     * @param int $user_id
     * @param bool $paginate
     * @return mixed
     */
    public static function getUserAddition(int $user_id, bool $paginate = false)
    {
        if ($paginate) {
            return MetaverseRanking::where('parent_id', $user_id)
                ->orderBy('basic', 'desc')
                ->simplePaginate();
        } else {
            return MetaverseRanking::where('parent_id', $user_id)
                ->sum('basic');
        }
    }

    /**
     * 增加当前用户的基础算力和推荐人的加成算力
     *
     * @param int $user_id
     * @param float $power
     */
    public static function increasePower(int $user_id, float $power)
    {
        $model = MetaverseRanking::where('user_id', $user_id)->latest()->first();
        /**
         * 先查询用户是否有使用记录，
         * 如果有则更新基本算力、总算力、会员等级
         * 如果没有则创建记录
         */
        if ($model) {
            $parent_id = $model->parent_id;

            $model->basic = bcadd($power, $model->basic, 8);
            $model->total = bcadd($model->basic, $model->addition, 8);
            // 计算会员等级变化
            $user_level = self::getUserLevel($user_id, $model->basic);
            $model->metaverse_user_level_id = $user_level ? $user_level->id : 0;
            $model->save();
        } else {
            $parent_id = self::userRecommenderId($user_id) ?: 0;
            // 当前用户的空间算力
            $userBigSpace = self::getUserBigSpace($user_id);
            $userSmallSpace = empty($userBigSpace['ids']) ? 0 : self::getUserSmallSpace($user_id, $userBigSpace['ids']);
            $userAddition = self::getUserAddition($user_id);
            $user_level = self::getUserLevel($user_id, $power);
            $team_level = self::getTeamLevel($userBigSpace['power'], $userSmallSpace);

            // 创建记录
            $model = MetaverseRanking::create([
                'parent_id' => $parent_id,
                'metaverse_user_level_id' => $user_level ? $user_level->id : 0,
                'metaverse_team_level_id' => $team_level ? $team_level->id : 0,
                'user_id' => $user_id,
                'basic' => $power,
                'addition' => $userAddition,
                'total' => bcadd($power, $userAddition, 8),
                'big_space' => $userBigSpace['power'],
                'small_space' => $userSmallSpace,
            ]);
        }

        // 当父级存在时，更新父级加成算力、总算力，并更新祖父级空间算力和团队等级
        if ($parent_id > 0 && $model) self::updateParentRanking($parent_id);
    }

    /**
     * 释放当前用的基础算力和父级的加成算力
     *
     * @param int $user_id
     * @param float $power
     */
    public static function releasePower(int $user_id, float $power)
    {
        $model = MetaverseRanking::where('user_id', $user_id)->latest()->first();

        if ($model) {
            // 释放用户基础算力
            $model->basic = bcsub($model->basic, $power, 8);
            // 防负值
            if ($model->basic < 0) $model->basic = 0;
            // 重新计算总算力
            $model->total = bcadd($model->basic, $model->addition, 8);
            $user_level = self::getUserLevel($user_id, $model->basic);
            // 重新计算会员等级
            $model->metaverse_user_level_id = $user_level ? $user_level->id : 0;

            if ($model->save()) {
                // 当父级存在时，更新释放父级加成算力、总算力，并更新祖父级空间算力和团队等级
                $parent_id = self::userRecommenderId($user_id) ?: 0;

                if ($parent_id > 0) self::updateParentRanking($parent_id);
            }
        }
    }

    /**
     * 处理父级关系，更新父级加成算力、总算力，并关联更新祖父级空间算力和团队等级
     *
     * @param int $parent_id
     */
    public static function updateParentRanking(int $parent_id)
    {
        $parent_model = MetaverseRanking::where('user_id', $parent_id)->latest()->first();
        if ($parent_model) {
            // 更新加成算力、总算力
            $parent_model->addition = self::getUserAddition($parent_id);
            $parent_model->total = bcadd($parent_model->addition, $parent_model->basic, 8);
            $parent_model->save();
            // 处理祖父级关系
            $grand_id = self::userRecommenderId($parent_id) ?: 0;

            // 如果祖父级存在，更新祖父级团队空间算力及团队等级
            if ($grand_id > 0) self::updateGrandRanking($grand_id);
        }
    }

    /**
     * 处理祖父级关系，孙级会员算力变化时会影响到其空间算力有团队等级
     *
     * @param int $grand_id
     */
    public static function updateGrandRanking(int $grand_id)
    {
        $grand_model = MetaverseRanking::where('user_id', $grand_id)->latest()->first();
        if ($grand_model) {
            $grandBigSpace = self::getUserBigSpace($grand_id);
            $grandSmallSpace = self::getUserSmallSpace($grand_id, $grandBigSpace['ids']);
            $team_level = self::getTeamLevel($grandBigSpace['power'], $grandSmallSpace);
            $grand_model->big_space = $grandBigSpace['power'];
            $grand_model->small_space = $grandSmallSpace;
            $grand_model->metaverse_team_level_id = $team_level ? $team_level->id : 0;
            $grand_model->save();
        }
    }

    /**
     * 获取大小空间算力值
     *
     * @param int $user_id
     * @return array
     */
    public static function getSpacePower(int $user_id): array
    {
        $space = [];

        // 获取用户团队成员ID
        $team_user_ids = Spread::where('parent_id', $user_id)->limit(500)->pluck('user_id');
        // 取加成算力TOP2作为大空间线
        $big_space = MetaverseRanking::whereIn('user_id', $team_user_ids)
            ->orderBy('addition', 'desc')
            ->limit(2)
            ->get();
        // 大空间总算力
        $space['big'] = $big_space->sum('addition');

        // 大空间用户IDS
        $big_space_user_ids = $big_space->pluck('user_id');
        // 小空间用户IDS
        $small_space_users = $team_user_ids->diff($big_space_user_ids)->all();
        $small_space = MetaverseRanking::whereIn('user_id', $small_space_users)
            ->orderBy('addition', 'desc')
            ->get();
        // 小空间总算力
        $space['small'] = $small_space->sum('addition');

        return $space;
    }

    /**
     * 会员推荐人ID
     *
     * @param int $user_id
     * @return mixed
     */
    public static function userRecommenderId(int $user_id)
    {
        return Spread::where('user_id', $user_id)->value('parent_id');
    }

    /**
     * 获取推荐人
     *
     * @param int $user_id
     * @return mixed
     */
    public static function userRecommender(int $user_id)
    {
        $parent_id = self::userRecommenderId($user_id);
        if ($parent_id) {
            return User::find($parent_id);
        }
        return null;
    }

    /**
     * 任务ID转数据
     *
     * @return false|string[]
     */
    public static function todayTaskToArray()
    {
        return explode(',', config('metaverse.miner_today_tasks'));
    }

    /**
     * 今日任务
     *
     * @param int $user_id
     * @return mixed
     */
    public static function todayTasks(int $user_id)
    {
        if (empty(self::todayTaskToArray())) return [];
        return Task::select('name', 'thumbnail', 'id', 'attribute')
            ->withCount(['task_reward_log' => function (Builder $query) use ($user_id) {
                $query->where('user_id', $user_id)
                    ->whereDate('created_at', now()->toDateString());
            }])
            ->whereIn('id', self::todayTaskToArray())
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * 验证当日任务是否已完成
     *
     * @param int $user_id
     * @return bool
     */
    public static function verifyTaskIsCompleted(int $user_id): bool
    {
        return count(self::todayTaskToArray()) === self::todayTaskCompleted($user_id);
    }

    /**
     * 获取已完成任务数
     *
     * @param int $user_id
     * @return int
     */
    public static function todayTaskCompleted(int $user_id): int
    {
        $tasks = self::todayTaskToArray();
        $isOk = 0;
        // 循环读取任务，出一条未完成，即返回未通过
        foreach ($tasks as $task) {
            // 取任务每日上限作为完成条件
            $item = Task::where('id', $task)->value('attribute');
            // 今日完成了几次任务
            $reward_count = TaskRewardLog::where('task_id', $task)
                ->where('user_id', $user_id)
                ->whereDate('created_at', now()->toDateString())
                ->count();
            if ($item[0] == $reward_count) {
                $isOk++;
            }
        }
        return $isOk;
    }

    /**
     * 任务进度
     *
     * @param $a
     * @param $b
     * @return string
     */
    public static function taskProgress($a, $b): string
    {
        if ($a == 0 || $b == 0) {
            return '0.00';
        }
        $x = bcdiv($a, $b, 4);
        if ($x == 0) {
            return '0.00';
        }
        return bcmul(100, $x, 2);
    }

    /**
     * 成功返回
     *
     * @param $data
     * @param string $message
     * @param int $code
     * @param int $status
     * @return JsonResponse
     */
    public static function success($data, string $message = '', int $code = 0, int $status = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'code' => $code
        ], $status);
    }

    /**
     * 失败返回
     *
     * @param $data
     * @param string $message
     * @param int $code
     * @param int $status
     * @return JsonResponse
     */
    public static function error($data, string $message = '', int $code = 422, int $status = 422): JsonResponse
    {
        return response()->json(
            [
                'errors' => $data,
                'message' => $message,
                'code' => $code,
            ],
            $status
        );
    }
}
