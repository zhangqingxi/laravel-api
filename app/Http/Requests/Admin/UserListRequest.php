<?php

namespace App\Http\Requests\Admin;


/**
 * @property string|null $keyword 关键词
 * @property int|null $status 状态
 * @property int|null $page 当前页
 * @property int|null $page_size 条数
 * @property string|null $field 排序字段
 * @property int|null $sort 排序值
 */
class UserListRequest extends BaseRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keyword' => 'nullable',
            'status' => 'nullable|in:0,1',
            'page' => 'nullable',
            'page_size' => 'nullable',
            'field' => 'nullable',
            'sort' => 'nullable|in:asc,desc',

        ];
    }

    public function attributes(): array
    {
        return [
            'keyword' => message('nickname', 'admin'),
            'page' => message('page'),
            'page_size' => message('page_size'),
            'field' => message('sort_field'),
            'sort' => message('sort_value'),
        ];
    }

}
