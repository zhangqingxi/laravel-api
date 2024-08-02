<?php

namespace App\Http\Requests\Admin;


/**
 * @property int $id 文件ID
 * @property string|null $name 文件名称
 */
class FileUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:admin.files,id',
            'name' => 'nullable|string|max:12',
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
