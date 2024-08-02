<?php

namespace App\Http\Requests\Admin;

/**
 * @property int $id 上级菜单ID
 * @property int|null $sort 排序
 * @property string|null $icon 菜单图标
 */
class MenuUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:admin.menus,id',
            'sort' => 'nullable|numeric|max:99',
            'icon' => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => message('menu', 'admin'),
            'sort' => message('sort', 'admin'),
        ];
    }

}
