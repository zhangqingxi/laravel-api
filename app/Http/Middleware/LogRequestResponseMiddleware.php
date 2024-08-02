<?php

namespace App\Http\Middleware;

use App\Jobs\Admin\ProcessRequestLog;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

/**
 * 日志请求响应中间件
 *
 * @package App\Http\Middleware
 * @autor Qasim
 * @time 2023/6/27 16:06
 */
class LogRequestResponseMiddleware
{

    /**
     * 处理传入的请求
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {


        // 生成全局唯一的请求 ID
        $requestId = (string) Str::uuid();

        $request->attributes->set('request_id', $requestId);

        $response = $next($request);

        // 检查请求是否被重复, 查看日志的请求不需要记录
        if (!$request->attributes->get('is_duplicate') && !str_contains($request->route()->getPrefix(), 'log')) {

            $this->log($request, $response);
        }

        return $response;
    }

    /**
     * 记录请求和响应数据
     *
     * @param Request $request 请求数据
     * @param JsonResponse|Response $response 响应数据
     */
    private function log(Request $request, JsonResponse|Response $response): void
    {

        //获取指定的headers
        $headers = [
            'X-AES-KEY' => $request->header('x-aes-key'),
            'Content-Type' => $request->header('content-type'),
            'Authorization' => $request->header('authorization'),
            'User-Agent' => $request->header('user-agent'),
            'X-Request-ID' => $request->header('x-request-id'),
        ];

        $requestData = $request->attributes->get('request_data');

        if($files = $request->file()){ //涉及文件上传
            foreach ($files as $file){
                $requestData['files'][] = [
                    'name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'size_text' => format_bytes($file->getSize()),
                    'extension' => $file->getClientOriginalExtension(),
                    'hash' => hash_file('sha256', $file->path()),
                ];

            }
        }

        $logData = [
            'request_id' => $request->attributes->get('request_id'),
            'host' => $request->getHost(),
            'url' => $request->url(),
            'controller' => class_basename($request->route()->getController()),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'headers' => $headers,
            'http_status' => $response->status(),
            'request_data' => $requestData,
            'encrypt_request_data' => $request->attributes->get('encrypt_request_data'),
            'response_data' => $request->attributes->get('response_data') ?? $response->getData(true),
            'encrypt_response_data' => $request->attributes->get('encrypt_response_data'),
        ];

        //异常数据
        if ($response->exception) {
            $logData['exception_data'] = [
                'code' => $response->exception->getCode(),
                'msg' => $response->exception->getMessage(),
                'file' => $response->exception->getFile(),
                'line' => $response->exception->getLine(),
            ];
        }

        ProcessRequestLog::dispatch($logData)->onConnection('sync');
    }
}
