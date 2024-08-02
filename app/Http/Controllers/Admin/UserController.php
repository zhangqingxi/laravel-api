<?php

namespace App\Http\Controllers\Admin;

use App\Constants\AdminStatusCodes;
use App\Http\Requests\Admin\UserAddRequest;
use App\Http\Requests\Admin\UserBatchRequest;
use App\Http\Requests\Admin\UserDeleteRequest;
use App\Http\Requests\Admin\UserForgetPasswordRequest;
use App\Http\Requests\Admin\UserListRequest;
use App\Http\Requests\Admin\UserUpdateInfoRequest;
use App\Http\Requests\Admin\UserUpdatePasswordRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\Admin\Admin;
use App\Models\Admin\File;
use App\Models\Admin\Menu;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\AdminException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

/**
 * 用户控制器
 * @Auther  Qasim
 * @date 2023/7/02
 */
class UserController extends BaseController
{

    //用户列表
    public function index(UserListRequest $request)
    {
        //加入分页
        $model = Admin::when($request->keyword, function ($query) use ($request){
                        return $query->where('nickname', 'like', '%' . $request->keyword . '%')
                            ->orWhere('account', 'like', '%' . $request->keyword . '%')
                            ->orWhere('email', 'like', '%' . $request->keyword . '%');
                    })->when(is_numeric($request->status), function ($query) use ($request){
                        return $query->where('status', $request->status);
                    })->when($request->field && $request->sort, function ($query) use($request) {
                        // 转换排序字段 对应数据库字段
                        $field = $this->convertSortField($request->field);
                        return $query->orderBy($field, $request->sort);
                    });

        //分页
        $admins = $model->paginate($request->page_size);

        // 获取分页数据的底层集合
        $items = $admins->getCollection();

        //总条数
        $items->each(function ($admin) {


            $avatar = '';

            if($file = $admin->avatar()->first()){

                $avatar = $file->url;
            }

            //设置头像
            $admin->setAttribute('avatar', $avatar);
            $admin->setAttribute('register_time', $admin->created_at->format('Y-m-d H:i:s'));
            $admin->setAttribute('last_login_time', $admin->last_login_at ? $admin->last_login_at->format('Y-m-d H:i:s') : '');
            $admin->setAttribute('roles', $admin->roles);
        });

        $admins = [
            'total' => $admins->total(),
            'items' => $items->toArray(),
        ];

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('fetch_success'), $admins);
    }

    /**
     * 用户
     * @return JsonResponse
     * @throws AdminException
     */
    public function info(): JsonResponse
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {

            throw new AdminException($this->getMessage('token_invalid'), AdminStatusCodes::UNAUTHORIZED);
        }

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('fetch_success'), [
                'userid' => $admin->id,
                'phone' => $admin->phone,
                'avatar' => $admin->avatar ? $admin->avatar->url : '',
                'nickname' => $admin->nickname,
                'account'=> $admin->account,
                'email'=> $admin->email,
                'register_time'=> $admin->created_at->format('Y-m-d H:i:s'),
                'last_login_time'=> $admin->last_login_at->format('Y-m-d H:i:s'),
                'last_login_address'=> $admin->getLastLoginAddressAttribute(),
            ]
        );
    }

    /**
     * 更新用户信息
     * @param UserUpdateInfoRequest $request
     * @return JsonResponse
     */
    public function updateInfo(UserUpdateInfoRequest $request)
    {

        $admin = Auth::guard('admin')->user();

        $admin->nickname = $request->nickname ?? $admin->nickname;
        $admin->email = $request->email ?? $admin->email;
        $admin->phone = $request->phone ?? $admin->phone;

        //头像
        if($request->avatar){

            $file = File::find($request->avatar);

            // 使用关联关系保存文件关联信息
            $file->associations()->create([
                'model_id' => $admin->id,
                'model_name' => $admin::class,
            ]);
        }

        $admin->save();

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('update_success'));
    }

    /**
     * 更新用户信息
     * @param UserUpdateRequest $request
     * @return JsonResponse
     * @throws AdminException
     */
    public function update(UserUpdateRequest $request)
    {
        $admin = Admin::find($request->id);

        if($admin->id === 1){

            throw new AdminException($this->getMessage('operation_not_allowed'), AdminStatusCodes::USER_NOT_ALLOWED);
        }

        //验证修改后的邮箱是否唯一
        if($admin->email !== $request->email && Admin::where('email', $request->email)->exists()){

            throw new AdminException($this->getMessage('email_exists'), AdminStatusCodes::EMAIL_EXIST);
        }

        //验证修改后的手机是否唯一
        if($admin->phone !== $request->phone && Admin::where('phone', $request->phone)->exists()){

            throw new AdminException($this->getMessage('phone_exists'), AdminStatusCodes::PHONE_EXIST);
        }

        $admin->nickname = $request->nickname ?? $admin->nickname;
        $admin->email = $request->email ?? $admin->email;
        $admin->created_at = $request->register_time ?? $admin->created_at;
        $admin->status = $request->status ?? $admin->status;
        $admin->phone = $request->phone ?? $admin->phone;
        $admin->password = $request->password ? Hash::make($request->password) : $admin->password;

        //头像
        if($request->avatar){

            $file = File::find($request->avatar);

            // 使用关联关系保存文件关联信息
            $file->associations()->create([
                'model_id' => $admin->id,
                'model_name' => $admin::class,
            ]);
        }

        //更新角色
        if($request->roles){

            $admin->roles()->sync($request->roles);
        }

        $admin->save();

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('update_success'));
    }

    /**
     * 批量操作用户信息
     * @param UserBatchRequest $request
     * @return JsonResponse
     */
    public function batch(UserBatchRequest $request)
    {

        foreach ($request->ids as $id){

            $admin = Admin::find($id);

            if($admin->id === 1){

                continue;
            }

            if($admin){
                switch ($request->type){

                    case 'enable':
                        $admin->status = 1;
                        $admin->save();
                        break;
                    case 'disable':
                        $admin->status = 0;
                        $admin->save();
                        break;
                    case 'delete':
                        $admin->delete();
                        break;
                }
            }
        }

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('update_success'));
    }

    /**
     * 添加用户
     * @param UserAddRequest $request
     * @return JsonResponse
     */
    public function add(UserAddRequest $request): JsonResponse
    {

        Admin::create([
            "account" => $request->account,
            "avatar" => $request->avatar ?? 0,
            "email" => $request->email,
            "nickname" => $request->nickname,
            "password" => Hash::make($request->password),
            "phone" => $request->phone,
            "status" => $request->status,
            "created_at" => $request->register_time
        ]);

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('add_success'));
    }

    /**
     * 删除用户
     * @param UserDeleteRequest $request
     * @return JsonResponse
     * @throws AdminException
     */
    public function delete(UserDeleteRequest $request): JsonResponse
    {

        $admin = Admin::find($request->id);

        if($admin->id === 1){

            throw new AdminException($this->getMessage('operation_not_allowed'), AdminStatusCodes::USER_NOT_ALLOWED);
        }

        $admin->delete();

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('delete_success'));
    }


    /**
     * 用户菜单
     * @return JsonResponse
     * @throws AdminException
     */
    public function menus()
    {
        $admin = Auth::guard('admin')->user();

        // 定义要传递的作用域
        $scopes = ['sort', 'fields', 'status'];

        // 设置模型的作用域
        Menu::setScopes($scopes);

        Menu::setFields(['id', 'pid', 'path', 'name', 'component', 'icon']);

        // 加载用户第一层次权限
        $menus = $admin->roles->flatMap(function ($role) use ($scopes) {
            return $role->menus()->scopes(array_merge($scopes, ['root']))->get();
        })->unique('id')->values();

        // 检查下级菜单权限
        $menus = $menus->map(function ($menu) use ($admin) {
            // 过滤菜单的子项
            $menu->children = $menu->allChildren->filter(function ($childMenu) use ($admin) {
                // 检查二级菜单的角色权限
                if ($childMenu->roles->intersect($admin->roles)->isEmpty()) {
                    return false;
                }

                // 检查三级菜单的角色权限，并收集路径
                $permissions = $childMenu->allChildren->filter(function ($grandChildMenu) use (&$permissions, $admin) {
                    if ($grandChildMenu->roles->intersect($admin->roles)->isEmpty()) {
                        return false;
                    }

                    $permissions[] = $grandChildMenu->path;
                    return true;
                });

                $childMenu->allChildren = $permissions;

                $childMenu->permissions = $permissions->pluck('path');

                // 移除无用的属性
                unset($childMenu->allChildren, $childMenu->roles);

                return true;
            })->values();

            // 移除无用的属性
            unset($menu->allChildren, $menu->roles);

            return $menu;
        })->values();

        if($menus->isEmpty()){

            throw new AdminException($this->getMessage('user_menus_not_found'), AdminStatusCodes::USER_MENUS_NOT_FOUND);
        }

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('fetch_success'), $menus);
    }

    /**
     * 忘记密码
     * @param UserForgetPasswordRequest $request
     * @return JsonResponse
     * @throws AdminException
     */
    public function forgetPassword(UserForgetPasswordRequest $request): JsonResponse
    {

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {

            throw new AdminException($this->getMessage('email_not_exist'), AdminStatusCodes::EMAIL_NOT_EXIST);
        }


        $admin->password = Hash::make($request->password);
        $admin->save();

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('update_success'));
    }

    /**
     * 更新用户密码
     * @param UserUpdatePasswordRequest $request
     * @return JsonResponse
     * @throws AdminException
     */
    public function updatePassword(UserUpdatePasswordRequest $request): JsonResponse
    {

        $admin = Auth::guard('admin')->user();

        //验证旧密码
        if (!Hash::check($request->old_password, $admin->password)) {

            throw new AdminException($this->getMessage('password_error'), AdminStatusCodes::USER_PASSWORD_INVALID);
        }

        $admin->password = Hash::make($request->new_password);

        $admin->save();

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('update_success'));
    }
}
