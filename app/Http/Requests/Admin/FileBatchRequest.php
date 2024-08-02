<?php

namespace App\Http\Requests\Admin;


/**
 * @property array $ids 文件IDs
 * @property string $type 操作类型
 */
class FileBatchRequest extends BaseRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => 'required|array',
            'type' => 'required|in:delete,unused',
        ];
    }

    public function attributes(): array
    {
        return [
            'ids' => message('batch_ids', 'admin'),
            'type' => message('batch_type', 'admin'),
        ];
    }

}
