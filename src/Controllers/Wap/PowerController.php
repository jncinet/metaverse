<?php
/**
 * 矿机
 */

namespace Jncinet\Metaverse\Controllers\Wap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Jncinet\Metaverse\Models\MetaverseMachine;
use Jncinet\Metaverse\Models\MetaversePower;
use Jncinet\Metaverse\Requests\PowerRequest;
use Illuminate\Http\JsonResponse;
use \Exception;
use Jncinet\Metaverse\Support;
use Illuminate\Database\Eloquent\Builder;

class PowerController extends Controller
{
    public function __construct()
    {
        App::setLocale('en');
    }

    // 下单记录
    public function index(Request $request)
    {
        $machines = MetaverseMachine::select('name', 'id')
            ->withCount([
                'metaverse_powers' => function (Builder $query) {
                    $query->where('user_id', Auth::id());
                }
            ])->get();

        $where = [
            ['user_id', Auth::id()],
        ];

        $id = $request->query('id', 0);

        if ($id > 0) {
            $where[] = ['metaverse_machine_id', $id];
        }

        $powers = MetaversePower::with('metaverse_machine')
            ->where($where)
            ->orderBy('id', 'desc')
            ->simplePaginate();

        return view('metaverse::power.index', compact('powers', 'machines', 'id'));
    }

    /**
     * 创建订单
     *
     * @param PowerRequest $request
     * @return JsonResponse
     */
    public function store(PowerRequest $request): JsonResponse
    {
        $data = $request->only(['metaverse_machine_id', 'quantity']);

        $data['user_id'] = Auth::id();

        try {
            // 查询矿机
            $machine = MetaverseMachine::select('count', 'price', 'power')
                ->where('id', $data['metaverse_machine_id'])
                ->first();
            if (!$machine) {
                throw new Exception(trans('metaverse::machine.not_exist'), 422);
            }

            if ($machine->price == 0 && MetaversePower::where('user_id', $data['user_id'])
                    ->where('metaverse_machine_id', $data['metaverse_machine_id'])
                    ->count() > 0) {
                throw new Exception(trans('metaverse::machine.claimed'), 422);
            }

            // 验证是否输入过交易密码
            if (!session('isVerifySafePassword', false)) {
                if (empty(Auth::user()->safe_password)) {
                    // 未设置安全密码
                    throw new Exception(trans('metaverse::metaverse.set_safe_pwd'), 424);
                }
                // 未验证安全密码
                throw new Exception(trans('metaverse::metaverse.safe_pwd_placeholder'), 423);
            } else {
                // 每次都需要输入安全密码
                session(['isVerifySafePassword' => false]);
            }

            // 数据快照
            $data['count'] = $machine->count;
            $data['price'] = $machine->price;
            $data['power'] = $machine->power;

            $data['remaining_count'] = $machine->count;

            // 计算需支付金额
            $data['total_price'] = bcmul($data['price'], $data['quantity'], 2);
            // 支付金额
            $isPaid = Support::payBalance($data['user_id'], $data['total_price']);
            // 如果支付失败
            if (!$isPaid) {
                throw new Exception(trans('metaverse::metaverse.pay_error'), 422);
            }
            // 计算算力值
            $data['total_power'] = bcmul($data['power'], $data['quantity'], 8);

            $model = MetaversePower::create($data);

            if ($model) {
                // 统计用户基础算力和父级的加成算力
                Support::increasePower(Auth::id(), $model->total_power);
            }

            return Support::success($model, trans('metaverse::machine.successful'));
        } catch (Exception $exception) {
            return Support::error($data, $exception->getMessage(), $exception->getCode());
        }
    }
}
