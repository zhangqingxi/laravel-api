<?php

namespace App\Http\Requests\Admin;


/**
 * @property string|null $nickname 昵称
 * @property int|null $avatar 头像文件
 * @property string|null $email 邮箱
 * @property string|null $phone 电话
 */
class UserUpdateInfoRequest extends BaseRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nickname' => 'nullable|min:2|max:16',
            'avatar' => 'nullable|exists:admin.files,id',
            'email' => 'nullable|email',
            'phone' => 'nullable|regex:/^1[3456789]\d{9}$/',
        ];
    }

    public function attributes(): array
    {
        return [
            'avatar' => message('avatar_file', 'admin'),
            'nickname' => message('nickname', 'admin'),
            'email' => message('email', 'admin'),
            'phone' => message('phone', 'admin'),
        ];
    }

}
