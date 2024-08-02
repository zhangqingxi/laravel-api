<?php

namespace App\Http\Requests\Admin;


/**
 * @property string $name 角色名称
 * @property string $remark 备注
 * @property int $status 状态
 * @property array $menus 菜单
 */
class RoleAddRequest extends BaseRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|min:4|max:16|unique:admin.roles,name',
            'remark' => 'required|min:8|max:150',
            'status' => 'required|in:0,1',
            'menus' => 'nullable|array',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => message('role_name', 'admin'),
            'remark' => message('role_remark', 'admin'),
            'status' => message('status', 'admin'),
            'menus' => message('menu', 'admin'),
        ];
    }

}
