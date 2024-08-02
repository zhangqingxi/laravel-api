<?php

namespace App\Http\Requests\Admin;



/**
 * @property int|null $pid 上级菜单ID
 * @property string $name 菜单名称
 * @property string|null $path 菜单路径
 * @property string|null $icon 菜单图标
 * @property int $visible 是否可见
 */
class CreateMenuRequest extends BaseRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pid' => 'nullable|numeric|exists:menus,id',
            'name' => 'required|string|max:255',
            'path' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'visible' => 'required|numeric|in:0,1',
        ];
    }
}
