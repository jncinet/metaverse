<?php
/**
 * 兑出
 */

namespace Jncinet\Metaverse\Controllers\Wap;

use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Jncinet\Metaverse\Models\MetaverseExchange;
use Jncinet\Metaverse\Models\MetaverseRanking;
use Jncinet\Metaverse\Support;
use Illuminate\Http\JsonResponse;
use Exception;
use GuzzleHttp\Client;

class ConvertController extends Controller
{
    public function __construct()
    {
        App::setLocale('en');
    }

    /**
     * 生成签名
     *
     * @param $username
     * @param $user_id
     * @return string
     */
    private function makeSign($username, $user_id): string
    {
        return sha1($username . '&' . $user_id . '&' . config('metaverse.miner_convert_api_key'));
    }

    /**
     * 兑出
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $api = config('metaverse.miner_convert_api');
            if (empty($api)) {
                throw new Exception(trans('metaverse::convert.not_opened'));
            }
            $amount = (float)$request->input('amount');
            $user = Auth::user();
            $integral = $user->account->integral;
            if ($integral < $amount) {
                throw new Exception(trans('metaverse::metaverse.user_balance_low'));
            }

            $ranking = MetaverseRanking::with('metaverse_user_level')
                ->where('user_id', $user->id)
                ->first();
            if (!$ranking || empty($ranking->api_user_id) || empty($ranking->api_username)) {
                throw new Exception(trans('metaverse::convert.unbound_account'), 423);
            }

            // 禁止兑出
            if ($ranking->metaverse_user_level->gas_fee_rate >= 100) {
                throw new Exception(trans('metaverse::convert.permission_denied'));
            }

            // 计算燃料费
            $gasFee = bcdiv($ranking->metaverse_user_level->gas_fee_rate, 100, 4);
            $gasFee = bcmul($amount, $gasFee, 8);

            // 第三方平台真实收到的金额
            $realAmount = bcsub($amount, $gasFee, 2);

            // 真实金额太小
            if ($realAmount <= 0) {
                throw new Exception(trans('metaverse::convert.amount_small'));
            }

            // 记录兑出日志
            MetaverseExchange::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'metaverse_user_level_id' => $ranking->metaverse_user_level_id,
                'rate' => $ranking->metaverse_user_level->gas_fee_rate,
                'fees' => $gasFee,
            ]);

            // 扣除支付金额
            if (!Support::exchangeBalance(Auth::id(), $amount)) {
                throw new Exception(trans('metaverse::convert.convert_failed'));
            }

            // 请求接口
            $client = new Client(['timeout' => 30.0, 'verify' => false]);
            $response = $client->request('POST', $api, [
                'form_params' => [
                    'email' => $ranking->api_username,
                    'user_id' => $ranking->api_user_id,
                    'amount' => $realAmount,
                    'sign' => $this->makeSign($ranking->api_username, $ranking->api_user_id),
                ]
            ]);

            if ($response->getReasonPhrase() == 'OK') {
                $data = json_decode((string)$response->getBody(), true);

                if ($data['code'] != 1) {
                    throw new Exception($data['msg'], $data['code']);
                }

                return Support::success($data['data']);
            }

            throw new Exception(trans('metaverse::convert.convert_failed'));
        } catch (Exception $exception) {
            return Support::error('', $exception->getMessage(), $exception->getCode());
        } catch (GuzzleException $e) {
            return Support::error('', $e->getMessage(), $e->getCode());
        }
    }

    /**
     * 绑定账号
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function bindUser(Request $request): JsonResponse
    {
        try {
            if (Hash::check($request->input('safe_password'), Auth::user()->safe_password)) {
                throw new Exception(trans('metaverse::metaverse.safe_password_failed'));
            }

            $user_id = (int)$request->input('user_id');
            $username = $request->input('username');

            if (empty($user_id) && empty($username)) {
                throw new Exception(trans('metaverse::metaverse.invalid_data'));
            }

            $ranking = MetaverseRanking::where('user_id', Auth::id())->first();
            if ($ranking) {
                $ranking->api_user_id = $user_id;
                $ranking->api_username = $username;
                $ranking->save();
            } else {
                $parent_id = Support::userRecommenderId(Auth::id()) ?: 0;
                MetaverseRanking::create([
                    'user_id' => Auth::id(),
                    'basic' => 0,
                    'addition' => 0,
                    'total' => 0,
                    'big_space' => 0,
                    'small_space' => 0,
                    'parent_id' => $parent_id,
                    'metaverse_user_level_id' => 0,
                    'metaverse_team_level_id' => 0,
                    'api_user_id' => $user_id,
                    'api_username' => $username,
                ]);
            }

            return Support::success('OK');
        } catch (Exception $exception) {
            return Support::error('ERROR', $exception->getMessage(), $exception->getCode());
        }
    }
}
