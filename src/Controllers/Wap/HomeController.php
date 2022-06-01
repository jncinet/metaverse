<?php
/**
 * 首页
 */

namespace Jncinet\Metaverse\Controllers\Wap;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Jncinet\Metaverse\Models\MetaverseRanking;
use Jncinet\Metaverse\Support;

class HomeController extends Controller
{
    public function __construct()
    {
        App::setLocale('en');
    }

    public function index()
    {
        $user = Auth::user();
        // 排行表，缓存用户总算力
        $ranking = MetaverseRanking::with('metaverse_user_level')
            ->where('user_id', Auth::id())
            ->first();
        if ($ranking) {
            // 会员等级燃料费
            $user_gas_fee_rate = $ranking->metaverse_user_level
                ? $ranking->metaverse_user_level->gas_fee_rate
                : 100;
            // 基础算力
            $basic_energy_value = $ranking->basic;
            // 加成算力
            $addition_energy_value = $ranking->addition;
        } else {
            $user_gas_fee_rate = 100;
            $basic_energy_value = 0;
            $addition_energy_value = 0;
        }
        // 今日任务
        $tasks = Support::todayTaskToArray();
        // 任务总数
        $task_count = count($tasks);
        // 今日任务
        $task_items = Support::todayTasks(Auth::id());
        // 完成进度百分比值
        $task_progress = Support::taskProgress(Support::todayTaskCompleted(Auth::id()), $task_count);

        return view(
            'metaverse::index',
            compact('basic_energy_value', 'addition_energy_value', 'user',
                'task_items', 'task_count', 'task_progress', 'user_gas_fee_rate')
        );
    }
}
