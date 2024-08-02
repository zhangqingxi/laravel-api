<?php

namespace App\Http\Requests\Admin;


/**
 * @property string $email 邮箱
 * @property string $password 密码
 */
class UserForgetPasswordRequest extends BaseRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string|min:8|max:32',
        ];
    }
}
