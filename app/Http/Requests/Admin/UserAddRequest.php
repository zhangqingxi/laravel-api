<?php

namespace App\Http\Requests\Admin;


/**
 * @property string $account 账号
 * @property string $password 密码
 * @property string $nickname 昵称
 * @property int|null $avatar 头像文件
 * @property string $email 邮箱
 * @property string $phone 电话
 * @property string $register_time 注册时间
 */
class UserAddRequest extends BaseRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account' => 'required|min:4|max:16|unique:admin.admins,account',
            'nickname' => 'required|min:2|max:16',
            'password' =>  'required|string|min:8|max:32',
            'avatar' => 'nullable|exists:admin.files,id',
            'email' => 'required|email|unique:admin.admins,email',
            'phone' => 'required|regex:/^1[3456789]\d{9}$/|unique:admin.admins,phone',
            'register_time' => 'nullable|date_format:Y-m-d H:i:s'
        ];
    }

    public function attributes(): array
    {
        return [
            'account' => message('account', 'admin'),
            'password' => message('password', 'admin'),
            'avatar' => message('avatar_file', 'admin'),
            'nickname' => message('nickname', 'admin'),
            'email' => message('email', 'admin'),
            'phone' => message('phone', 'admin'),
            'register_time' => message('register_time', 'admin'),
        ];
    }

}
