<?php

namespace App\Http\Requests\Admin;

/**
 * @property int $id 用户ID
 */
class UserDeleteRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:admin.admins,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => message('user', 'admin'),
        ];
    }

}
