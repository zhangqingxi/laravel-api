<?php

namespace App\Http\Requests\Admin;


/**
 * @property int $id 用户ID
 * @property string|null $nickname 昵称
 * @property int|null $avatar 头像文件
 * @property int|null $status 状态
 * @property string|null $email 邮箱
 * @property string|null $password 密码
 * @property string|null $phone 电话
 * @property string|null $register_time 注册时间
 * @property array|null $roles 角色
 */
class UserUpdateRequest extends BaseRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:admin.admins,id',
            'nickname' => 'nullable|min:2|max:16',
            'password' =>  'nullable|string|min:8|max:32',
            'avatar' => 'nullable|exists:admin.files,id',
            'status' => 'nullable|in:0,1',
            'email' => 'nullable|email',
            'phone' => 'nullable|regex:/^1[3456789]\d{9}$/',
            'register_time' => 'nullable|date_format:Y-m-d H:i:s',
            'roles' => 'nullable|array',
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => message('user', 'admin'),
            'avatar' => message('avatar_file', 'admin'),
            'nickname' => message('nickname', 'admin'),
            'email' => message('email', 'admin'),
            'phone' => message('phone', 'admin'),
            'register_time' => message('register_time', 'admin'),
            'password' => message('password', 'admin'),
            'roles' => message('role', 'admin'),
        ];
    }

}
