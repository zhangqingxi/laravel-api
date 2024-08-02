<?php

namespace App\Http\Requests\Admin;



use App\Models\Admin\Menu;

/**
 * @property int $id 菜单ID
 */
class MenuDeleteRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:admin.menus,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => message('menu', 'admin'),
        ];
    }

}
