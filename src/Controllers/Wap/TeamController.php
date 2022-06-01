<?php
/**
 * 社区
 */

namespace Jncinet\Metaverse\Controllers\Wap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Jncinet\Metaverse\Models\MetaverseRanking;
use Jncinet\Metaverse\Models\MetaverseTeamLevel;
use App\Services\QrCodeService;
use Jncinet\Metaverse\Support;

class TeamController extends Controller
{
    public function __construct()
    {
        App::setLocale('en');
    }

    public function index()
    {
        $user = Auth::user();

        $ranking_model = MetaverseRanking::with(['metaverse_user_level', 'metaverse_team_level'])
            ->where('user_id', $user->id)
            ->first();

        if (!$ranking_model) {
            return redirect()->route('metaverse.index');
        }

        // 会员等级
        $user_level = $ranking_model->metaverse_user_level;

        // 推广码
        $qrcode = (new QrCodeService())->userQrCode($user->id, $user->avatar);

        // 推荐人
        $recommender = Support::userRecommender(Auth::id());
        if ($recommender) {
            $recommender = $recommender->nickname ?: 'UID: ' . $recommender->id;
        } else {
            $recommender = trans('metaverse::team-level.founder');
        }

        // 算力排行
        $user_ranking = MetaverseRanking::where('total', '>', $ranking_model->total)->count();
        $user_ranking += 1;

        // 取加成算力TOP2作为大空间线
        $big_space = Support::getUserBigSpace(Auth::id(), true);
        // 大空间总算力
        $big_space_total_addition = $big_space->sum('addition');
        // 大空间IDS
        $big_space_user_ids = $big_space->pluck('id')->toArray();
        // 小空间用户IDS
        $small_space = Support::getUserSmallSpace(Auth::id(), $big_space_user_ids, true);
        // 小空间总算力
        $small_space_total_addition = Support::getUserSmallSpace(Auth::id(), $big_space_user_ids);
        // 总算力
        $total_addition = bcadd($big_space_total_addition, $small_space_total_addition, 8);

        // 团队等级
        $team_level = $ranking_model->metaverse_team_level
            ? $ranking_model->metaverse_team_level->sort
            : 0;

        // 数字格式化
        $big_space_total_addition = number_format($big_space_total_addition, 4);
        $small_space_total_addition = number_format($small_space_total_addition, 4);
        $total_addition = number_format($total_addition, 4);

        return view(
            'metaverse::team.index',
            compact('user', 'user_level', 'qrcode', 'recommender', 'user_ranking',
                'team_level', 'total_addition', 'big_space', 'big_space_total_addition', 'small_space',
                'small_space_total_addition')
        );
    }

    // 直推下线提供的加成能量列表
    public function addition(Request $request)
    {
        // 所有算力贡献榜
        $items = MetaverseRanking::with('user')
            ->where('basic', '>', 0)
            ->where('parent_id', Auth::id())
            ->orderBy('basic', 'desc')
            ->simplePaginate();

        return view('metaverse::team.addition', compact('items'));
    }

    // 选择团队等级
    public function selectTeamLevel()
    {
        return MetaverseTeamLevel::select('id', 'name as text')->get();
    }
}
