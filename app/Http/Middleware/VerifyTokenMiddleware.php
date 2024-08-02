<?php

namespace App\Http\Middleware;

use App\Constants\CommonStatusCodes;
use App\Exceptions\CustomException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Token验证中间件
 * @Auther Qasim
 * @date 2023/7/1
 */
class VerifyTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     * @throws CustomException
     */
    public function handle(Request $request, Closure $next): mixed
    {

        //获取token
        $token = $request->bearerToken();

        if (!$token) {

            throw new CustomException(message('token_not_provided'), CommonStatusCodes::TOKEN_NOT_PROVIDED);
        }

        // 通过 Sanctum 验证 Token
        if (!Auth::guard('sanctum')->check()) {

            throw new CustomException(message('token_invalid'), CommonStatusCodes::TOKEN_INVALID);
        }

        return $next($request);
    }
}
