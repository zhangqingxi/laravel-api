<?php

namespace App\Listeners;

use App\Events\ModelLogEvent;
use App\Models\Admin\OperationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModelLogListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(ModelLogEvent $event): void
    {
        //操作日志
        OperationLog::query()->create([
            'admin_id' =>  $event->adminId,
            'content' => $event->content,
            'table_name' => $event->tableName,
            'table_id' => $event->tableId,
            'url' => request()->url(),
            'method' => request()->getMethod(),
            'ip' => request()->getClientIp(),
        ]);
    }
}
