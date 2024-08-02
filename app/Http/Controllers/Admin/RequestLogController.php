<?php

namespace App\Http\Controllers\Admin;

use App\Constants\AdminStatusCodes;
use App\Http\Requests\Admin\LogBatchRequest;
use App\Http\Requests\Admin\RequestLogIndexRequest;
use App\Models\Admin\RequestLog;
use Illuminate\Http\JsonResponse;

/**
 * 请求日志控制器
 * @Auther Qasim
 * @date 2023/7/30
 */
class RequestLogController extends BaseController
{

    /**
     * 获取日志列表
     * @param RequestLogIndexRequest $request
     * @return JsonResponse
     */
    public function index(RequestLogIndexRequest $request): JsonResponse
    {

        $logs = RequestLog::when(($request->start_date && $request->end_date), function ($query) use ($request) {
            return $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        })->when($request->method, function ($query) use($request){
            return $query->where('method', $request->method);
        })->paginate($request->page_size);

        // 获取分页数据的底层集合
        $items = $logs->getCollection();

        //总条数
        $items->each(function ($log) {

            $log->setAttribute('ip_address', $log->getIpAddressAttribute());
            $log->setAttribute('create_time', $log->created_at->format('Y-m-d H:i:s'));
        });

        $logs = [
            'total' => $logs->total(),
            'items' => $items->toArray(),
        ];

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('fetch_success'), $logs);
    }


    /**
     * 批量清空日志
     * @param LogBatchRequest $request
     * @return JsonResponse
     */
    public function delete(LogBatchRequest $request)
    {

        RequestLog::when($request->type, function ($query) use($request){
            if($request->type === 'all') return $query;
            return $query->where('method', $request->type);
        })->delete();

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('delete_success'));
    }
}
