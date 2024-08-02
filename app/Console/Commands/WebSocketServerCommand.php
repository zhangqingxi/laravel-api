<?php

namespace App\Console\Commands;

use App\Constants\AdminStatusCodes;
use App\Constants\CommonStatusCodes;
use App\Models\Admin\Admin;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Laravel\Sanctum\PersonalAccessToken;
use Swoole\Timer;
use Swoole\WebSocket\Server as SwooleWebSocketServer;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;

class WebSocketServerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start Swoole WebSocket Server';

    // 存储客户端用户信息的数组
    protected array $clientUserInfo = [];

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $webSocketServer = new SwooleWebSocketServer("127.0.0.1", 9502);

        $webSocketServer->on("start", function (SwooleWebSocketServer $server) {

            echo "Swoole WebSocket Server started at ws: {$server->host}:{$server->port}\n";
        });

        $webSocketServer->on("open", function (SwooleWebSocketServer $server, Request $request) {

            echo "连接已打开：{$request->fd}\n";
        });

        $webSocketServer->on("message", function (SwooleWebSocketServer $server, Frame $frame) {
            $fd = $frame->fd;
            $data = json_decode($frame->data, true);
            var_dump($data);
            //处理用户数据
            $type = $data['type'] ?? '';

            switch ($type)
            {
                case 'userInfo':
                    $this->handleUserInfoType($server, $fd, $data);
                    break;
                case 'chat':
                    $chatData = $data['chatData'];
                    $this->clientUserInfo[$fd]['chatData'] = $chatData;
                    break;
                default:
                    $this->clientUserInfo[$fd]['otherData'] = $data;
                    break;
            }
        });

        $webSocketServer->on("close", function (SwooleWebSocketServer $server, int $fd) {
            if (isset($this->clientUserInfo[$fd])) {
                $userInfo = $this->clientUserInfo[$fd];
                $userid = $userInfo['userid'];
                echo "连接已关闭：{$fd}，用户 ID：{$userid}\n";
                unset($this->clientUserInfo[$fd]);
            } else {
                echo "连接已关闭：{$fd}，未知用户\n";
            }
        });

        //定时器
        $this->timerTick($webSocketServer);

        $webSocketServer->start();
    }

    /**
     * 获取用户Token信息
     * @param int $userid
     * @return PersonalAccessToken|null
     */
    public function getLatestTokenInfoFromSanctum(int $userid): PersonalAccessToken|null
    {
        // 查询 personal_access_tokens 表中指定用户 ID 的最新 token
        return PersonalAccessToken::
            whereTokenableId($userid)
            ->whereName('admin-token')
            ->whereTokenableType(Admin::class)
            ->orderBy('id', 'desc')
            ->select(['token', 'expires_at'])
            ->first();

    }

    public function timerTick(SwooleWebSocketServer $webSocketServer): void
    {
        // 添加定时器，每分钟检查 Token 是否即将到期
        Timer::tick(60000, function () use ($webSocketServer) {
            $now = Carbon::now();
            foreach ($this->clientUserInfo as $fd => $userInfo) {

                $tokenInfo = $this->getLatestTokenInfoFromSanctum(1);

                if(!$tokenInfo) continue;

                $tokenExpiration = $now->diffInSeconds($tokenInfo->expires_at);

                // 如果 Token 剩余有效时间不足5分钟，发送更新通知到客户端
                if ($tokenExpiration <= 600) {
                    $connectionInfo = $webSocketServer->connection_info($fd);
                    if ($connectionInfo['websocket_status'] === WEBSOCKET_STATUS_FRAME) {
                        $webSocketServer->push($fd, json_encode([
                            'type' => 'ok',
                            'code' => CommonStatusCodes::TOKEN_EXPIRED_REFRESH,
                            'message' => message('token_expired_refresh'),
                        ]));
                    }
                }
            }
        });
    }

    /**
     * 处理用户信息类型
     * @param SwooleWebSocketServer $server
     * @param int $fd
     * @param array $data
     * @return void
     */
    public function handleUserInfoType(SwooleWebSocketServer $server, int $fd, array $data): void
    {
        //查询Token
        $tokenInfo = $this->getLatestTokenInfoFromSanctum($data['userid']);

        //无效用户
        if(!$tokenInfo){
            // 通知Token失效并断开连接
            $server->push($fd, json_encode([
                'type' => 'close',
                'code' => CommonStatusCodes::TOKEN_INVALID,
                'message' => message('token_invalid'),
            ]));
        }

        //保存用户信息
        $this->clientUserInfo[$fd] = [
            'userid' => $data['userid'],
            'token' => $data['token'],
        ];

        //查询是否存在多用户登录
        var_dump($this->clientUserInfo);
        foreach ($this->clientUserInfo as $fd => $userInfo) {
            list($id, $token) = explode('|', $userInfo['token']);
            if ($userInfo['userid'] === $data['userid']) {
                var_dump([$fd, $token, hash('sha256', $token), $tokenInfo->token]);
                //验证Token
                if(hash('sha256', $token) !== $tokenInfo->token){
                    $connectionInfo = $server->connection_info($fd);
                    if ($connectionInfo['websocket_status'] === WEBSOCKET_STATUS_FRAME) {
                        // 通知异地登录并断开连接
                        $server->push($fd, json_encode([
                            'type' => 'close',
                            'code' => AdminStatusCodes::USER_LOGIN_OTHER_DEVICE,
                            'message' => message('account_login_other_device'),
                        ]));
                    }
                }
            }
        }
    }
}
