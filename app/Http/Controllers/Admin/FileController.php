<?php
namespace App\Http\Controllers\Admin;

use App\Constants\AdminStatusCodes;
use App\Exceptions\AdminException;
use App\Http\Requests\Admin\FileBatchRequest;
use App\Http\Requests\Admin\FileDeleteRequest;
use App\Http\Requests\Admin\FileIndexRequest;
use App\Http\Requests\Admin\FileUnusedRequest;
use App\Http\Requests\Admin\FileUpdateRequest;
use App\Http\Requests\Admin\FileUploadRequest;
use App\Models\Admin\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * 文件控制器
 * @Auther Qasim
 * @date 2023/6/29
 */
class FileController extends BaseController
{
    /**
     * 文件列表
     * @param FileIndexRequest $request
     * @return JsonResponse
     */
    public function index(FileIndexRequest $request): JsonResponse
    {

        $files = File::when(($request->start_date && $request->end_date), function ($query) use ($request) {
                        return $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
                    })->when($request->type, function ($query) use($request){
                        return $query->where('type', $request->type);
                    })->when($request->field && $request->sort, function ($query) use($request) {
                        // 转换排序字段 对应数据库字段
                        $field = $this->convertSortField($request->field);
                        return $query->orderBy($field, $request->sort);
                    })->paginate($request->page_size);

        // 获取分页数据的底层集合
        $items = $files->getCollection();

        //总条数
        $items->each(function ($file) {

            $associations = [];

            if($files = $file->associations()->get()){

                $associations = $files->map(function ($association) {
                    return ['model_name' => $association->model_name, 'model_id' => $association->model_id];
                });
            }

            $file->setAttribute('associations', $associations);
            $file->setAttribute('url', $file->url);
            $file->setAttribute('create_time', $file->created_at->format('Y-m-d H:i:s'));
        });

        $files = [
            'total' => $files->total(),
            'items' => $items->toArray(),
        ];

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('fetch_success'), $files);
    }

    /**
     * 取消文件关联
     * @param FileUnusedRequest $request
     * @return JsonResponse
     */
    public function unused(FileUnusedRequest $request): JsonResponse
    {

        $file = File::find($request->id);

        $file->associations()->delete();

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('update_success'));
    }

    /**
     * 修改文件
     * @param FileUpdateRequest $request
     * @return JsonResponse
     */
    public function update(FileUpdateRequest $request): JsonResponse
    {

        $file = File::find($request->id);

        $file->name = $request->name ?? $file->name;

        $file->save();

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('update_success'));
    }

    /**
     * 删除文件
     * @param FileDeleteRequest $request
     * @return JsonResponse
     * @throws AdminException
     */
    public function delete(FileDeleteRequest $request): JsonResponse
    {

        $file = File::find($request->id);

        if($file->associations->isNotEmpty()){

            throw new AdminException($this->getMessage('file_has_association'), AdminStatusCodes::FILE_HAS_ASSOCIATION);
        }

        $file->delete();

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('delete_success'));
    }

    /**
     * 批量操作资源
     * @param FileBatchRequest $request
     * @return JsonResponse
     */
    public function batch(FileBatchRequest $request)
    {

        foreach ($request->ids as $id){

            $file = File::find($id);

            if($file){
                switch ($request->type){

                    case 'unused':
                        $file->associations()->delete();
                        break;
                    case 'delete':
                        $file->delete();
                        break;
                }
            }
        }

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('update_success'));
    }

    /**
     * 文件上传
     */
    public function upload(FileUploadRequest $request)
    {
        // 检测是否是分片上传
        if ($request->has('chunk_index') && $request->has('total_chunks')) {

            return $this->handleChunkUpload($request);
        }

        list($type, $extension) = explode('/', $request->type);

        $files = $request->file('file');

        if (!is_array($files)) {

            $files = [$files];
        }

        $uploadedFiles = [];
        foreach ($files as $file) {
            $path = $file->store('uploads/' . $type . '/' . date('Ymd'), 'admin');

            $fileModel = File::create([
                'name' => $file->getClientOriginalName(),
                'type' => $type,
                'mime_type' => $request->type,
                'drive' => 'admin',
                'path' => $path,
                'size' => $file->getSize(),
                'size_text' => format_bytes($file->getSize()),
                'extension' => $extension,
                'hash' => hash_file('sha256', $file->path()),
                'uploaded_by' => Auth::guard('admin')->id(),
            ]);

            $uploadedFiles[] = [
                'id' => $fileModel->id,
                'url' => $fileModel->url,
            ];
        }

        //单文件上传返回数据
        if(count($uploadedFiles) === 1) {

            $uploadedFiles = $uploadedFiles[0];
        }

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('upload_success'), $uploadedFiles);
    }

    /**
     * 处理分片上传
     */
    protected function handleChunkUpload(FileUploadRequest $request)
    {

        list($type, $extension) = explode('/', $request->type);

        $file = $request->file('file');
        $chunkPath = $type .'/'. $request->name;

        Storage::disk('chunks')->put($chunkPath . '/' . $request->chunk_index, file_get_contents($file->getPathname()));

        // 如果是最后一个分片，进行文件合并
        if (intval($request->chunk_index) === intval($request->total_chunks - 1)) {

            $path = 'uploads/' . $request->type . '/' . date('Ymd') . '/' . Str::random(40) . '.' . $request->format;

            //目录不存在就创建
            if (!Storage::disk('admin')->exists(dirname($path))) {

                Storage::disk('admin')->makeDirectory(dirname($path));
            }

            $file =  Storage::disk('admin')->path($path);

            // 合并文件
            $out = fopen($file, 'wb');

            for ($i = 0; $i < $request->total_chunks; $i++) {
                $chunkFile = Storage::disk('chunks')->path($chunkPath . '/' .$i);
                $in = fopen($chunkFile, 'rb');
                while ($buff = fread($in, 4096)) {
                    fwrite($out, $buff);
                }
                fclose($in);
                // 删除分片文件
                Storage::disk('chunks')->delete($chunkPath . '/' .$i);
            }
            fclose($out);

            $fileModel = File::create([
                'name' => $request->filename,
                'type' => $type,
                'mime_type' => $request->type,
                'drive' => 'admin',
                'path' => $path,
                'size' => filesize($file),
                'size_text' => format_bytes(filesize($file)),
                'extension' => $extension,
                'hash' => hash_file('sha256', $file),
                'uploaded_by' => Auth::guard('admin')->id(),
            ]);

            return json(AdminStatusCodes::SUCCESS, $this->getMessage('upload_success'), [
                'id' => $fileModel->id,
                'url' => $fileModel->url,
            ]);
        }

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('chunk_upload_success'));

//        $finalPath = "uploads/" . $request->type . '/' . date('Ymd') . '/' . $fileName;
//        $finalFile = Storage::disk('chunks')->path($finalPath);
//        dd($request);
//        $chunkIndex = $request->input('chunkIndex');
//        $chunk = $request->file('file');
//
//        $chunkDir = storage_path('app/uploads/chunks/' . $fileName);
//        if (!is_dir($chunkDir)) {
//            mkdir($chunkDir, 0777, true);
//        }
//
//        $chunkPath = $chunkDir . '/' . $chunkIndex;
//        $chunk->move($chunkDir, $chunkIndex);
//
//        // 如果所有分片都上传完毕
//        if ($chunkIndex == $totalChunks - 1) {
//            $finalPath = storage_path('app/uploads/' . $fileName);
//            $file = fopen($finalPath, 'ab');
//
//            for ($i = 0; $i < $totalChunks; $i++) {
//                $chunkPath = $chunkDir . '/' . $i;
//                $chunkContent = file_get_contents($chunkPath);
//                fwrite($file, $chunkContent);
//                unlink($chunkPath); // 删除分片
//            }
//
//            fclose($file);
//            rmdir($chunkDir); // 删除分片目录
//
//            // 保存文件信息到数据库
//            $fileModel = File::create([
//                'name' => $fileName,
//                'type' => $request->type,
//                'mime_type' => mime_content_type($finalPath),
//                'drive' => 'admin',
//                'path' => 'uploads/' . $fileName,
//                'size' => filesize($finalPath),
//                'size_text' => format_bytes(filesize($finalPath)),
//                'extension' => pathinfo($fileName, PATHINFO_EXTENSION),
//                'hash' => hash_file('sha256', $finalPath),
//                'uploaded_by' => Auth::guard('admin')->id(),
//            ]);
//
//            return json(AdminStatusCodes::SUCCESS, $this->getMessage('upload_success'), [
//                'id' => $fileModel->id,
//                'url' => $fileModel->url,
//            ]);
//        }
//
//        return json(AdminStatusCodes::SUCCESS, $this->getMessage('chunk_upload_success'));
    }

}
