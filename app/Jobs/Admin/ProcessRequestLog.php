<?php

namespace App\Jobs\Admin;

use App\Models\Admin\RequestLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * 处理请求日志的队列任务
 * @作者 Qasim
 * @日期 2023/6/28
 */
class ProcessRequestLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $logData;

    /**
     * 创建一个新的任务实例
     * @param array $logData 日志数据
     */
    public function __construct(array $logData)
    {
        $this->logData = $logData;
    }

    /**
     * 任务执行逻辑
     * @return void
     */
    public function handle(): void
    {

        RequestLog::create($this->logData);
    }


    /**
     * 任务失败处理
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        //TODO
    }
}
