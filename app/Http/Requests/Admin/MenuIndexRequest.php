<?php

namespace App\Http\Requests\Admin;

/**
 * @property $string $name 菜单名称
 * @property int $status 状态
 */
class MenuIndexRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'nullable',
            'status' => 'nullable|in:0,1',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => message('menu_name', 'admin'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
