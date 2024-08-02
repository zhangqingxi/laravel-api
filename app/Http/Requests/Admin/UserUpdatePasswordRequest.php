<?php

namespace App\Http\Requests\Admin;


/**
 * @property string $old_password 旧密码
 * @property string $new_password 新密码
 */
class UserUpdatePasswordRequest extends BaseRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'old_password' => 'required|string|min:8|max:32',
            'new_password' => 'required|string|min:8|max:32',
        ];
    }

    public function attributes(): array
    {
        return [
            'old_password' => message('old_password'),
            'new_password' => message('new_password'),
        ];
    }
}
