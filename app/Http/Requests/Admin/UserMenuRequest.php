<?php

namespace App\Http\Requests\Admin;

use App\Models\Admin\Admin;

/**
 * @property Admin $admin 管理员
 */
class UserMenuRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'nullable|integer|exists:admins,id',
        ];
    }
}
