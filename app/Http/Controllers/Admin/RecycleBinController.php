<?php

namespace App\Http\Controllers\Admin;

use App\Constants\AdminStatusCodes;
use App\Http\Requests\Admin\RecycleBinBatchRequest;
use App\Http\Requests\Admin\RecycleBinDeleteRequest;
use App\Http\Requests\Admin\RecycleBinIndexRequest;
use App\Http\Requests\Admin\RecycleBinRestoreRequest;
use App\Models\Admin\RecycleBin;
use App\Exceptions\AdminException;
use Illuminate\Http\JsonResponse;

/**
 * 回收站控制器
 * @Auther  Qasim
 * @date 2023/7/31
 */
class RecycleBinController extends BaseController
{

    /**
     * 获取资源列表
     * @param RecycleBinIndexRequest $request
     * @return JsonResponse
     */
    public function index(RecycleBinIndexRequest $request)
    {
        $recycleBins = RecycleBin::when(($request->start_date && $request->end_date), function ($query) use ($request) {
            return $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        })->when($request->field && $request->sort, function ($query) use($request) {
            // 转换排序字段 对应数据库字段
            $field = $this->convertSortField($request->field);
            return $query->orderBy($field, $request->sort);
        })->paginate($request->page_size);


        // 获取分页数据的底层集合
        $items = $recycleBins->getCollection();

        //总条数
        $items->each(function ($log) {

            $log->setAttribute('create_time', $log->created_at->format('Y-m-d H:i:s'));
        });

        $recycleBins = [
            'total' => $recycleBins->total(),
            'items' => $items->toArray(),
        ];

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('fetch_success'), $recycleBins);
    }

    /**
     * 批量操作资源
     * @param RecycleBinBatchRequest $request
     * @return JsonResponse
     */
    public function batch(RecycleBinBatchRequest $request)
    {

        foreach ($request->ids as $id){

            $recycleBin = RecycleBin::find($id);

            if($recycleBin){
                switch ($request->type){

                    case 'restore':
                        app($recycleBin->table_name)->where('id', $recycleBin->table_id)->withTrashed()->restore();
                        $recycleBin->delete();
                        break;
                    case 'delete':
                        app($recycleBin->table_name)->where('id', $recycleBin->table_id)->withTrashed()->forceDelete();
                        $recycleBin->delete();
                        break;
                }
            }
        }

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('update_success'));
    }


    /**
     * 删除资源
     * @param RecycleBinDeleteRequest $request
     * @return JsonResponse
     * @throws AdminException
     * @throws \Throwable
     */
    public function delete(RecycleBinDeleteRequest $request): JsonResponse
    {

        $recycleBin = RecycleBin::find($request->id);

        app($recycleBin->table_name)->where('id', $recycleBin->table_id)->withTrashed()->forceDelete();

        $recycleBin->delete();

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('delete_success'));
    }

    /**
     * 恢复资源
     * @param RecycleBinRestoreRequest $request
     * @return JsonResponse
     * @throws AdminException
     * @throws \Throwable
     */
    public function restore(RecycleBinRestoreRequest $request): JsonResponse
    {

        $recycleBin = RecycleBin::find($request->id);

        app($recycleBin->table_name)->where('id', $recycleBin->table_id)->withTrashed()->restore();

        $recycleBin->delete();

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('update_success'));
    }

}
