<?php

namespace App\Http\Requests\Admin;

/**
 * @property array $ids 资源IDs
 * @property string $type 操作类型
 */
class RecycleBinBatchRequest extends BaseRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => 'required|array',
            'type' => 'required|in:delete,restore',
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
