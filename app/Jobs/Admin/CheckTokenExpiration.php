<?php

namespace App\Jobs\Admin;

use App\Constants\CommonStatusCodes;
use Hhxsv5\LaravelS\Swoole\Timer\CronJob;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\PersonalAccessToken;

class CheckTokenExpiration extends CronJob
{
    /*
     * 每隔6000ms执行一次
     */
    public function interval(): int
    {
        return 60000;
    }

    /**
     * 是否立即执行
     * @return bool
     */
    public function isImmediate(): bool
    {
        return false;
    }

    public function run(): void
    {
        // TODO: Implement run() method.
        $server = app('swoole');

        $now = Carbon::now();

        foreach ($server->wsTable as $fd => $user){

            //检查是否有效的客户端
            $wsServer = $server->connection_info($fd);

            if(!$wsServer || $wsServer['websocket_status'] !== WEBSOCKET_STATUS_FRAME){
                $server->wsTable->del($fd);
            }

            $tokenInfo = PersonalAccessToken::whereTokenableId($user['uid'])
                ->whereId($user['token_id'])
                ->where('token', hash('sha256', $user['token_value']))
                ->orderBy('id', 'desc')
                ->first();

            if(!$tokenInfo) {
                $server->wsTable->del($fd);
                continue;
            }

            $tokenExpiration = $now->diffInSeconds($tokenInfo->expires_at);

            // 如果 Token 剩余有效时间不足5分钟，发送更新通知到客户端
            if ($tokenExpiration <= 600) {

                $server->push($fd, json_encode([
                    'type' => 'ok',
                    'code' => CommonStatusCodes::TOKEN_EXPIRED_REFRESH,
                    'message' => message('token_expired_refresh'),
                ]));
            }
        }
    }
}
