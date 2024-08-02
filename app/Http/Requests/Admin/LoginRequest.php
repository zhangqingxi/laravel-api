<?php

namespace App\Http\Requests\Admin;


/**
 * @property string $account 登录账号
 * @property string $password 登录密码
 * @property boolean $nologin 免登录
 */
class LoginRequest extends BaseRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account' => 'required|string|min:4|max:16',
            'password' => 'required|string|min:8|max:32',
            'nologin' => 'required|boolean',
        ];
    }
}
