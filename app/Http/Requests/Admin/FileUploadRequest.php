<?php

namespace App\Http\Requests\Admin;

use App\Models\Admin\File;
use Illuminate\Http\UploadedFile;

/**
 * @property UploadedFile $file 文件
 * @property string $filename 文件名称
 * @property string $format 文件格式
 * @property string $type 文件类型
 * @property int|null $chunk_index 分片序号
 * @property int|null $total_chunks 总分片数
 */
class FileUploadRequest extends BaseRequest
{
    public function rules(): array
    {

        return [
            'file' => 'required|file|max:10240', // 10MB
            'type' => 'required|in:' . implode(',', File::$mimeTypes),
            'format' => 'required|in:' . implode(',', File::$fileFormats),
            'filename' => 'required|string',
            'chunk_index' => 'nullable|numeric|min:0',
            'total_chunks' => 'nullable|numeric|min:1|after_or_equal:chunk_index',
        ];

    }

    public function attributes(): array
    {
        return [
            'type' => message('file_type', 'admin'),
            'filename' => message('file_name', 'admin'),
            'format' => message('file_format', 'admin'),
            'chunk_index' => message('file_chunk_index', 'admin'),
            'total_chunks' => message('file_total_chunks', 'admin'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
