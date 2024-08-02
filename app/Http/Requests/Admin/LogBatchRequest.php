<?php

namespace App\Http\Requests\Admin;


/**
 * @property string $type 操作类型
 */
class LogBatchRequest extends BaseRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:get,post,put,delete,all',
        ];
    }

    public function attributes(): array
    {
        return [
            'type' => message('batch_type', 'admin'),
        ];
    }

}
