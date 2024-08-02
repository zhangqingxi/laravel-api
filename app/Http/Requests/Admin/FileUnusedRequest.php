<?php

namespace App\Http\Requests\Admin;

/**
 * @property int $id 文件ID
 */
class FileUnusedRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:admin.files,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => message('file', 'admin'),
            'name' => message('file_name', 'admin'),
        ];
    }

}
