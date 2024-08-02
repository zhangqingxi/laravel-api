<?php

namespace App\Http\Requests\Admin;


/**
 * @property string|null $start_date 开始日期
 * @property string|null $end_date 结束日期
 * @property int|null $page 当前页
 * @property int|null $page_size 条数
 * @property string|null $field 排序字段
 * @property int|null $sort 排序值
 */
class RecycleBinIndexRequest extends BaseRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => 'nullable|date_format:Y-m-d|before_or_equal:end_date',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'page' => 'nullable',
            'page_size' => 'nullable',
            'field' => 'nullable',
            'sort' => 'nullable|in:asc,desc',
        ];
    }

    public function attributes(): array
    {
        return [
            'start_date' => message('start_date'),
            'end_date' => message('end_date'),
            'page' => message('page'),
            'page_size' => message('page_size'),
            'field' => message('sort_field'),
            'sort' => message('sort_value'),
        ];
    }

}
