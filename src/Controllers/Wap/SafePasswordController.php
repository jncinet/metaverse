<?php
/**
 * 安全密码
 */

namespace Jncinet\Metaverse\Controllers\Wap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Exception;
use Jncinet\Metaverse\Support;
use Jncinet\Metaverse\Requests\SafePasswordRequest;
use Illuminate\Http\JsonResponse;

class SafePasswordController extends Controller
{
    public function __construct()
    {
        App::setLocale('en');
    }

    /**
     * 设置安全密码
     *
     * @param SafePasswordRequest $request
     * @return JsonResponse
     */
    public function setSafePassword(SafePasswordRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();

            // 验证当前登录密码是否正确
            if (!Hash::check($request->input('password'), $user->getAuthPassword())) {
                throw new Exception(trans('metaverse::metaverse.password_failed'), 422);
            }

            $user->safe_password = Hash::make($request->input('safe_password'));
            if (!$user->save()) {
                throw new Exception(trans('metaverse::metaverse.processing_failed'), 422);
            }
            return Support::success('', trans('metaverse::metaverse.processing_succeeded'));
        } catch (Exception $exception) {
            return Support::error('', $exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * 验证安全密码
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifySafePassword(Request $request): JsonResponse
    {
        if (Hash::check($request->input('safe_password'), Auth::user()->safe_password)) {
            session(['isVerifySafePassword' => true]);
            return Support::success('OK');
        }
        return Support::error('', trans('metaverse::metaverse.safe_password_failed'));
    }
}
