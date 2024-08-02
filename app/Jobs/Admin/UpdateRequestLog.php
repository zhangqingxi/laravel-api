<?php

namespace App\Jobs\Admin;

use App\Models\Admin\RequestLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * 处理请求日志更新的队列任务
 * @作者 Qasim
 * @日期 2023/6/29
 */
class UpdateRequestLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $requestId;
    protected string|null $responseData;

    /**
     * 创建一个新的任务实例
     * @param string $requestId 唯一日志ID
     * @param string|null $responseData
     */
    public function __construct(string $requestId, string|null $responseData)
    {
        $this->requestId = $requestId;
        $this->responseData = $responseData;
    }

    /**
     * 任务执行逻辑
     * @return void
     */
    public function handle(): void
    {

        RequestLog::where('request_id', $this->requestId)->update([
            'encrypt_response_data' => $this->responseData,
        ]);
    }

    /**
     * 任务失败处理
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        //TODO: 处理失败情况
    }
}
