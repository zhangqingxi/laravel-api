<?php

namespace App\Http\Controllers\Admin;

use App\Constants\MailTypes;
use App\Http\Requests\Admin\SendMailRequest;
use App\Http\Requests\Admin\VerifyMailRequest;
use App\Models\Admin\Admin;
use Illuminate\Http\JsonResponse;
use App\Exceptions\AdminException;
use App\Jobs\Admin\SendMailJob;
use App\Constants\AdminStatusCodes;

/**
 * 通用邮件控制器
 * @Auther Qasim
 * @date 2023/6/27
 */
class MailController extends BaseController
{
    /**
     * 发送邮件验证码
     * @param SendMailRequest $request
     * @return JsonResponse
     * @throws AdminException
     */
    public function send(SendMailRequest $request): JsonResponse
    {
        $admin = Admin::whereEmail($request->email)->first();

        //验证类型错误
        switch ($request->type){
            case MailTypes::RESET_PASSWORD:
            case MailTypes::CHANGE_PASSWORD:

                if (!$admin) {

                        throw new AdminException($this->getMessage('email_not_exist'), AdminStatusCodes::EMAIL_NOT_EXIST);
                    }
                    break;
            case MailTypes::BIND_EMAIL:
                if ($admin) {

                        throw new AdminException($this->getMessage('email_exist'), AdminStatusCodes::EMAIL_EXIST);
                    }
                    break;
            default:
                throw new AdminException($this->getMessage('email_type_not_allowed'), AdminStatusCodes::EMAIL_TYPE_NOT_ALLOWED);
        }

        //生成6位验证码带字母混合
        $code = $this->verificationCode(6);

        $key = $this->getCacheKey($request->email, $request->type);

        //设置hash
        $expirationSeconds = config('admin.mail.code_expiration') * 60;

        $this->redis->hset($key, 'code', $code);
        $this->redis->hset($key, 'used', 0);
        $this->redis->hset($key, 'expires_at', $expirationSeconds);
        $this->redis->expire($key, $expirationSeconds);

        // 异步发送邮件
        SendMailJob::dispatch($admin->email, $code, $request->type)->onConnection('admin');

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('email_sent_success'));
    }

    /**
     * 验证邮件验证码
     * @param VerifyMailRequest $request
     * @return JsonResponse
     * @throws AdminException
     */
    public function verify(VerifyMailRequest $request): JsonResponse
    {
        $key = $this->getCacheKey($request->email, $request->type);

        $data = $this->redis->hgetall($key);

        if (!$data || !isset($data['code']) || $data['code'] !== $request->code) {

            throw new AdminException($this->getMessage('email_invalid_code'), AdminStatusCodes::EMAIL_INVALID_CODE);
        }

        $used = intval($data['used']);
        if ($used >= 3) { // 最多使用3次

            throw new AdminException($this->getMessage('email_code_exceeded'), AdminStatusCodes::EMAIL_CODE_EXCEEDED);
        }

        // 验证通过，使用次数加1
        $this->redis->hset($key, 'used', $used + 1);

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('email_verify_success'));
    }

    /**
     * 生成缓存键
     * @param string $email
     * @param string $type
     * @return string
     */
    private function getCacheKey(string $email, string $type): string
    {
        return config('admin.mail.code_key').":" . $type. ":" .$email;
    }

    private function verificationCode(int $length = 4)
    {
        $characters = '0123456789abcdefghilkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $verificationCode = '';

        for ($i = 0; $i < $length; $i++) {

            $index = mt_rand(0, strlen($characters) - 1);
            $verificationCode .= $characters[$index];
        }

        return $verificationCode;
    }
}

