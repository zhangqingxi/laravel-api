<?php

namespace App\Listeners;

use App\Events\RequestResponseLogEvent;
use App\Jobs\Admin\ProcessRequestLogJob;
use App\Models\Admin\RequestLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RequestResponseLogListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RequestResponseLogEvent $event): void
    {
        //
        $request = $event->request;
        $response = $event->response;

        //获取指定的headers
        $headers = [
            'Accept-Language' => $request->header('accept-language'),
            'X-AES-KEY' => $request->header('x-aes-key'),
            'Content-Type' => $request->header('content-type'),
            'Authorization' => $request->header('authorization'),
            'User-Agent' => $request->header('user-agent'),
            'X-Request-ID' => $request->header('x-request-id'),
        ];

        # 必须有请求日志才记录
        $requestId = $headers['X-Request-ID'];

        if($requestId && !RequestLog::where('request_id', $requestId)->exists()) {

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
                'request_id' => $requestId,
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

            ProcessRequestLogJob::dispatch($logData)->onConnection('admin');
        }
    }
}
