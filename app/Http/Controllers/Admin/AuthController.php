<?php

namespace App\Http\Controllers\Admin;

use App\Constants\AdminStatusCodes;
use App\Http\Requests\Admin\LoginRequest;
use App\Jobs\Admin\UpdateLoginDetailsJob;
use App\Models\Admin\Admin;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\AdminException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * 管理员认证控制器
 * @Auther  Qasim
 * @date 2023/6/27
 */
class AuthController extends BaseController
{

    /**
     * 登录
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws AdminException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $admin = Admin::whereAccount($request->account)->first();

        if (!$admin) {

            throw new AdminException($this->getMessage('login_failed'), AdminStatusCodes::UNAUTHORIZED);
        }

        $lockoutTime = config('admin.login.lockout_time');
        $maxAttempts = config('admin.login.max_attempts');

        if ($admin->is_locked) {

            // 检查是否锁定时间已过
            if (now()->diffInMinutes($admin->last_failed_login_at) < $lockoutTime) {

                throw new AdminException($this->getMessage('account_locked'), AdminStatusCodes::ACCOUNT_LOCKED);
            } else {

                // 重置锁定状态和失败次数
                $admin->is_locked = 0;
                $admin->login_failed_attempts = 0;
                $admin->save();
            }
        }

        // 检查是否需要重置失败次数
        if ($admin->last_failed_attempts_reset_at && now()->diffInMinutes($admin->last_failed_attempts_reset_at) > $lockoutTime) {

            $admin->login_failed_attempts = 0;
            $admin->last_failed_attempts_reset_at = now();
            $admin->save();
        }

        if (!Hash::check($request->password, $admin->password)) {

            $admin->login_failed_attempts++;
            $admin->last_failed_login_at = now();
            $admin->last_failed_attempts_reset_at = now();

            if ($admin->login_failed_attempts >= $maxAttempts) {

                $admin->is_locked = 1;
            }
            $admin->save();

            throw new AdminException($this->getMessage('login_failed'), AdminStatusCodes::UNAUTHORIZED);
        }

        //删除已颁发的Token
        $admin->tokens()->delete();

        // 设置Token过期时间
        $tokenExpiration = config('admin.login.token_expiration');
        if($request->nologin){//免登录过期时间
            $tokenExpiration = config('admin.login.no_login');
        }

        $token = $admin->createToken('admin-token', ['*'], now()->addMinutes($tokenExpiration))->plainTextToken;
        // 重置失败次数和锁定状态
        $admin->login_failed_attempts = 0;
        $admin->is_locked = 0;
        $admin->last_failed_attempts_reset_at = null;
        $admin->save();

        // 异步更新登录信息
        UpdateLoginDetailsJob::dispatch($admin, $request->ip())->onConnection('admin');

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('login_success'), ['token' => $token]);
    }

    /**
     * 注销Token
     * @return JsonResponse
     * @throws AdminException
     */
    public function logout(): JsonResponse
    {

        $admin = Auth::guard('admin')->user();

        $admin->tokens()->delete();

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('logout_success'));
    }

    /**
     * 刷新 Token
     * @return JsonResponse
     * @throws AdminException
     */
    public function refresh(): JsonResponse
    {
        $admin = Auth::guard('admin')->user();

        /**
         * @var PersonalAccessToken $token
         */
        $token = $admin->currentAccessToken();

        // 延长当前令牌的有效期
        $token->expires_at = now()->addMinutes(config('admin.login.token_expiration'));

        $token->save();

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('refresh_success'));
    }
}
