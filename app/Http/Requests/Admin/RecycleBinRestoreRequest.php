<?php

namespace App\Http\Requests\Admin;

/**
 * @property int $id 资源ID
 */
class RecycleBinRestoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:admin.recycle_bins,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => message('recycle', 'admin'),
        ];
    }

}
