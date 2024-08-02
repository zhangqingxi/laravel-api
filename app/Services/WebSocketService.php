<?php
namespace App\Services;
use App\Constants\CommonStatusCodes;
use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use Laravel\Sanctum\PersonalAccessToken;
use Swoole\Http\Request;
use Swoole\Table;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;
class WebSocketService implements WebSocketHandlerInterface
{
    /**
     * @var Table $wsTable
     */
    private Table $wsTable;
    public function __construct()
    {
        $this->wsTable = app('swoole')->wsTable;
    }


    public function onOpen(Server $server, Request $request): void
    {
        echo "客户端已连接：{$request->fd}\n";
    }

    public function onMessage(Server $server, Frame $frame): void
    {

        $data = json_decode($frame->data, true);

        // 处理用户数据
        $type = $data['type'] ?? '';

        switch ($type)
        {
            case 'userInfo':
                $this->handleUserInfoType($server, $frame, $data);
                break;
            default:
                break;
        }
    }

    public function onClose(Server $server, $fd, $reactorId): void
    {

        echo "客户端已关闭：{$fd}\n";

        if($this->wsTable->has($fd)){

            $this->wsTable->del($fd);
        }
    }



    /**
     * 处理用户信息类型
     * @param Server $server
     * @param Frame $frame
     * @param array $data
     * @return void
     */
    private function handleUserInfoType(Server $server, Frame $frame, array $data): void
    {
        list($token_id, $token_value) = explode('|', $data['token']);

        // 验证用户是否存在
        if (!PersonalAccessToken::whereTokenableId($data['userid'])
            ->whereId($token_id)
            ->whereToken(hash('sha256', $token_value))
            ->exists()) {

            $server->push($frame->fd, json_encode([
                'type' => 'close',
                'code' => CommonStatusCodes::TOKEN_INVALID,
                'message' => 'Token invalid',
            ]));
            return;
        }

        // 保存用户信息
        $this->wsTable->set($frame->fd, [
            'uid' => $data['userid'],
            'token_id' => $token_id,
            'token_value' => $token_value,
        ]);

        // 是否异地登录
        foreach ($server->connections as $fd) {
            if ($fd === $frame->fd) {
                continue; // 跳过当前连接
            }

            $user = $this->wsTable->get($fd) ?: [];

            if ($user && $user['uid'] === $data['userid'] && $user['token_value'] !== $token_value) {

                // 通知其他设备用户已在此设备登录
                $server->push($fd, json_encode([
                    'type' => 'close',
                    'code' => CommonStatusCodes::TOKEN_EXPIRED_LOGIN_OTHER_DEVICE,
                    'message' => message('account_login_other_device'),
                ]));
            }
        }
    }
}
