<?php

namespace App\Exceptions;

use App\Constants\CommonStatusCodes;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;

/**
 * 自定义异常处理器
 * @Auther Qasim
 * @date 2023/6/28
 */
class Handler extends ExceptionHandler
{
    /**
     * 渲染异常为 HTTP 响应
     *
     * @param Request $request 请求
     * @param Throwable $e 异常
     * @return JsonResponse|Response
     * @throws Throwable
     */
    public function render($request, Throwable $e): JsonResponse|Response
    {
        // 检查异常类型并设置适当的消息和状态码
        $msg = message('general_error');
        $data = [];
        $code = CommonStatusCodes::EXCEPTION_ERROR;
        if ($e instanceof AdminException || $e instanceof CustomException) {

            $msg = $e->getMessage();
            $code = $e->getCode();
        } elseif ($e instanceof AuthenticationException) {

            $msg = message('token_invalid');
            $code = CommonStatusCodes::TOKEN_INVALID;
        } elseif ($e instanceof QueryException) {

            $msg = message('database_error');
            $code = CommonStatusCodes::DATABASE_ERROR;
        } elseif ($e instanceof ValidationException) {

            $msg = $e->getMessage();
            $code = CommonStatusCodes::VALIDATION_ERROR;
        } elseif ($e instanceof NotFoundHttpException) {

            $msg = message('not_found');
            $code = CommonStatusCodes::NOT_FOUNT_EXCEPTION;
        } elseif ($e instanceof MethodNotAllowedHttpException){

            $msg = message('method_not_allowed');
            $code = CommonStatusCodes::METHOD_NOT_ALLOWED;

        } elseif ($e instanceof RouteNotFoundException){

            $msg = message('route_not_found');
            $code = CommonStatusCodes::ROUTE_NOT_FOUND;
        }

//        // 设置异常信息到请求属性中
//        $request->attributes->set('exception_data', [
//            'code' => $e->getCode(),
//            'msg' => $e->getMessage(),
//            'file' => $e->getFile(),
//            'line' => $e->getLine(),
//        ]);

        return json($code, $msg, $data);

        // TODO: 其他非API请求的处理逻辑
//        return parent::render($request, $e);
    }
}
