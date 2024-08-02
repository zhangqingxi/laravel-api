<?php

namespace App\Http\Requests\Admin;


/**
 * @property int $id 角色ID
 * @property string|null $name 角色名称
 * @property string|null $remark 角色备注
 * @property int|null $status 状态
 * @property array|null $menus 菜单
 */
class RoleUpdateRequest extends BaseRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:admin.admins,id',
            'name' => 'nullable|min:2|max:16',
            'remark' =>  'nullable|string|min:8|max:150',
            'status' => 'nullable|in:0,1',
            'menus' => 'nullable|array',
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => message('role', 'admin'),
            'name' => message('role_name', 'admin'),
            'remark' => message('role_remark', 'admin'),
            'status' => message('status', 'admin'),
            'menus' => message('menu', 'admin'),
        ];
    }

}
