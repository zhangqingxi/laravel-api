<?php

namespace App\Http\Requests\Admin;

/**
 * @property int $id 角色ID
 */
class RoleDeleteRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:admin.roles,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => message('role', 'admin'),
        ];
    }

}
