<?php

namespace App\Http\Controllers\Admin;

use App\Constants\AdminStatusCodes;
use App\Exceptions\AdminException;
use App\Http\Requests\Admin\RoleAddRequest;
use App\Http\Requests\Admin\RoleBatchRequest;
use App\Http\Requests\Admin\RoleDeleteRequest;
use App\Http\Requests\Admin\RoleIndexRequest;
use App\Http\Requests\Admin\RoleUpdateRequest;
use App\Models\Admin\Role;
use Illuminate\Http\JsonResponse;

/**
 * 角色控制器
 * @Auther Qasim
 * @date 2023/7/25
 */
class RoleController extends BaseController
{
    /**
     * 角色列表
     * @param RoleIndexRequest $request
     * @return JsonResponse
     */
    public function index(RoleIndexRequest $request): JsonResponse
    {
        //加入分页
        $model = Role::when($request->keyword, function ($query) use ($request) {
                        return $query->where('name', 'like', '%' . $request->keyword . '%');
                    })->when(is_numeric($request->status), function ($query) use ($request) {
                        return $query->where('status', $request->status);
                    })->when($request->field && $request->sort, function ($query) use($request) {
                        // 转换排序字段 对应数据库字段
                        $field = $this->convertSortField($request->field);
                        return $query->orderBy($field, $request->sort);
                    });

        if($request->page){
            //分页
            $roles = $model->paginate($request->page_size);

            // 获取分页数据的底层集合
            $items = $roles->getCollection();

            //总条数
            $items->each(function ($role) {

                //菜单
                $role->setAttribute('menus', $role->menus);
                $role->setAttribute('create_time', $role->created_at->format('Y-m-d H:i:s'));
            });

            $role = [
                'total' => $roles->total(),
                'items' => $items->toArray(),
            ];
        }else{

            $role = $model->get();
        }

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('fetch_success'), $role);
    }

    /**
     * 添加角色
     * @param RoleAddRequest $request
     * @return JsonResponse
     */
    public function add(RoleAddRequest $request): JsonResponse
    {

        $role = Role::create([
            "name" => $request->name,
            "remark" => $request->remark,
            "status" => $request->status,
        ]);

        //菜单
        if($request->menus){

            $role->menus()->sync($request->menus);
        }

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('add_success'));
    }

    /**
     * 批量操作角色信息
     * @param RoleBatchRequest $request
     * @return JsonResponse
     */
    public function batch(RoleBatchRequest $request)
    {

        foreach ($request->ids as $id){

            $role = Role::find($id);

            if($role->id === 1){

                continue;
            }

            if($role){
                switch ($request->type){

                    case 'enable':
                        $role->status = 1;
                        $role->save();
                        break;
                    case 'disable':
                        $role->status = 0;
                        $role->save();
                        break;
                    case 'delete':
                        $role->delete();
                        break;
                }
            }
        }

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('update_success'));
    }

    /**
     * 删除角色
     * @param RoleDeleteRequest $request
     * @return JsonResponse
     * @throws AdminException
     */
    public function delete(RoleDeleteRequest $request): JsonResponse
    {

        $role = Role::find($request->id);

        if($role->id === 1){

            throw new AdminException($this->getMessage('operation_not_allowed'), AdminStatusCodes::ROLE_NOT_ALLOWED);
        }

        $role->delete();

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('delete_success'));
    }


    /**
     * 更新角色信息
     * @param RoleUpdateRequest $request
     * @return JsonResponse
     * @throws AdminException
     */
    public function update(RoleUpdateRequest $request)
    {
        $role = Role::find($request->id);

        if($role->id === 1){

            throw new AdminException($this->getMessage('operation_not_allowed'), AdminStatusCodes::ROLE_NOT_ALLOWED);
        }

        //验证修改后的角色名称是否唯一
        if($role->name !== $request->name && Role::whereName($request->name)->exists()){

            throw new AdminException($this->getMessage('role_exist'), AdminStatusCodes::ROLE_EXIST);
        }

        $role->name = $request->name ?? $role->name;
        $role->remark = $request->remark ?? $role->remark;
        $role->status = $request->status ?? $role->status;

        if($request->menus){

            $role->menus()->sync($request->menus);
        }

        $role->save();

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('update_success'));
    }
}
