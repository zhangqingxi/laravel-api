<?php

namespace App\Http\Controllers\Admin;

use App\Constants\AdminStatusCodes;
use Illuminate\Http\JsonResponse;

/**
 * 初始化控制器
 * @Auther Qasim
 * @date 2023/6/29
 */
class IndexController extends BaseController
{

    /**
     * 初始化方法
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {

        $publicKey = file_get_contents(config('admin.encryption.rsa_public_key'));

        $data = [
            'public_key' => base64_encode($publicKey),
            'encryption_enabled' => config('admin.enable_encryption'),
            'api_url' => config('admin.url') . '/' . config('admin.route_prefix'),
            'ws_url' => config('admin.ws.url') . ':' . config('admin.ws.port'),
        ];

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('fetch_success'), $data);
    }
}
