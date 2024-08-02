<?php

namespace App\Http\Requests\Admin;


/**
 * @property string|null $start_date 开始日期
 * @property string|null $end_date 结束日期
 * @property string|null $file_type 文件类型
 * @property int|null $page 当前页
 * @property int|null $page_size 条数
 */
class FileIndexRequest extends BaseRequest
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
            'type' => 'nullable|in:image,video,doc',
            'page' => 'nullable',
            'page_size' => 'nullable'
        ];
    }

    public function attributes(): array
    {
        return [
            'start_date' => message('start_date'),
            'end_date' => message('end_date'),
            'page' => message('page'),
            'type' => message('file_type'),
            'page_size' => message('page_size'),
        ];
    }

}
